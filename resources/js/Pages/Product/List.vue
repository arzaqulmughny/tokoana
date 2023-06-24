<script>
import axios from "axios";
export default {
    layout: MainLayout,
    data() {
        return {
            params: {
                page: 1,
                search: "",
                sortBy: "name"
            },
            modal: {
                addNewProductModal: false,
                viewProductModal: null,
                editProductModal: null
            },
            selectAll: false,
            selected: []
        };
    },
    methods: {
        async getDetailData(id) {
            const { data } = await axios.get(`/product/list/${id}`);
            return data;
        },
        toggleItemAction(event) {
            Array.from(document.getElementsByClassName("item-action__list--active")).forEach(element => {
                if (event.target.nextElementSibling != element) {
                    element.classList.toggle("item-action__list--active");
                }
            });

            event.target.nextElementSibling.classList.toggle("item-action__list--active");
        },
        removeItem(id) {
            event.target.parentElement.parentElement.children[1].checked = false;
            if (confirm("Delete this item?")) {
                router.delete(`/product/list/${id}`);
            }
        },
        deleteSelected() {
            if (confirm("Delete selected item?")) {
                this.selected.forEach(id => {
                    router.delete(`/product/list/${id}`);
                    this.selected = [];
                    this.selectAll = false;
                });
            }
        }
    },
    watch: {
        params: {
            handler() {
                if (this.timer) {
                    clearTimeout(this.timer);
                }
                this.timer = setTimeout(() => {
                    router.visit(this.getUrlWithParams, {
                        only: ["data"],
                        preserveState: true,
                        preserveScroll: true
                    });
                }, 1000);
            },
            deep: true
        },
        selectAll() {
            if (this.selectAll) {
                this.$page.props.data.data.forEach(item => {
                    this.selected.push(item.id);
                });
            } else {
                this.selected = [];
            }
        },
        searchAndSort() {
            this.params.page = 1;
        }
    },
    computed: {
        getUrlWithParams() {
            let URL = `${window.location.pathname}?`;
            const params = Object.keys(this.params);

            params.forEach(param => {
                if (this.params[param].length != 0) {
                    URL += `&${param}=${this.params[param]}`;
                }
            });

            return URL;
        }
    },
    created() {
        let url = window.location.href;
        url = new URL(url);

        url.searchParams.get("page") ? (this.params.page = url.searchParams.get("page")) : null;
        url.searchParams.get("search") ? (this.params.search = url.searchParams.get("search")) : null;
        url.searchParams.get("sort") ? (this.params.sortBy = url.searchParams.get("sort")) : null;
    }
};
</script>

<script setup>
import Button from "@/Components/Button.vue";
import AddNewProductModal from "@/Components/AddNewProductModal.vue";
import ViewProductModal from "@/Components/ViewProductModal.vue";
import EditProductModal from "@/Components/EditProductModal.vue";
import Table from "@/Components/Table.vue";
import RowMenu from "@/Components/RowMenu.vue";
import { Link, router, Head } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import SearchBar from "@/Components/SearchBar.vue";
import SelectCustom from "@/Components/SelectCustom.vue";
</script>

<template>
    <Head>
        <title>Product List</title>
    </Head>
    <MainLayout>
        <div class="content">
            <div class="content__header">
                <h1 class="content__title">Product List</h1>
                <Button
                    :text="'Add new product'"
                    :icon="'iconoir-plus'"
                    :variant="'primary'"
                    @click="this.modal.addNewProductModal = true"
                    v-if="$page.props.user.user_level === 1"
                />
            </div>
            <div class="actions">
                <div class="actions__left">
                    <SelectCustom :text="'Sort'" :variant="'secondary'" :icon="'iconoir-sort'" :menuPosition="'right'">
                        <span class="select-custom__option" @click="this.params.sortBy = 'name'">Name</span>
                        <span class="select-custom__option" @click="this.params.sortBy = 'latest'">Latest</span>
                        <span class="select-custom__option" @click="this.params.sortBy = 'oldest'">Oldest</span>
                    </SelectCustom>
                    <Button
                        :text="'Remove selected (' + this.selected.length + ')'"
                        v-if="this.selected.length !== 0"
                        :icon="'iconoir-cancel'"
                        :variant="'secondary'"
                        @click="this.deleteSelected"
                    />
                    <Button
                        :text="this.params.search"
                        :icon="'iconoir-cancel'"
                        v-if="this.params.search"
                        @click="this.params.search = ''"
                    />
                </div>
                <div class="actions__right">
                    <SearchBar :name="'search'" :placeholder="'Search product...'" v-model:value="this.params.search" />
                </div>
            </div>
            <Table>
                <template #head>
                    <tr>
                        <td>
                            <input type="checkbox" v-model="this.selectAll" class="table__select" />
                        </td>
                        <td>BARCODE</td>
                        <td>NAME</td>
                        <td>CATEGORY</td>
                        <td>UNIT</td>
                        <td>PRICE</td>
                        <td>STOCK</td>
                        <td>ACTIONS</td>
                    </tr>
                </template>
                <template #body>
                    <tr data-empty="true" v-if="$page.props.data.data.length == 0">
                        <td colspan="100">No items available</td>
                    </tr>
                    <tr v-for="item in $page.props.data.data">
                        <td :key="item.id">
                            <input type="checkbox" :value="item.id" v-model="this.selected" />
                        </td>
                        <td>{{ item.barcode || "" }}</td>
                        <td>{{ item.name || "" }}</td>
                        <td>{{ item.category.name || "" }}</td>
                        <td>{{ item.unit.name || "" }}</td>
                        <td>{{ item.price || "" }}</td>
                        <td>{{ item.stock }}</td>
                        <td>
                            <RowMenu>
                                <Button
                                    :text="'View'"
                                    :variant="'clear'"
                                    @click="async () => (this.modal.viewProductModal = await this.getDetailData(item.id))"
                                />
                                <Button
                                    :text="'Edit'"
                                    :variant="'clear'"
                                    @click="async () => (this.modal.editProductModal = await this.getDetailData(item.id))"
                                    v-if="$page.props.user.user_level === 1"
                                />
                                <Button
                                    :text="'Remove'"
                                    :variant="'clear'"
                                    @click="this.removeItem(item.id)"
                                    v-if="$page.props.user.user_level === 1"
                                />
                            </RowMenu>
                        </td>
                    </tr>
                </template>
            </Table>
            <span class="total">{{ $page.props.data.total }} total items available</span>
            <div class="pagination">
                <Button
                    :icon="'iconoir-nav-arrow-left'"
                    :variant="'secondary'"
                    @click="this.params.page > 1 ? (this.params.page = this.params.page - 1) : null"
                />

                <div class="pagination__number">
                    <Button :text="1" :variant="1 == $page.props.data.current_page ? 'primary' : 'clear'" @click="this.params.page = 1" />
                    <Button v-if="$page.props.data.current_page != 1" :text="$page.props.data.current_page" :variant="'primary'" />
                    <Button
                        :text="$page.props.data.current_page + 1"
                        @click="this.params.page = $page.props.data.current_page + 1"
                        v-if="$page.props.data.last_page >= $page.props.data.current_page + 1"
                        :variant="'clear'"
                    />
                    <Button
                        :text="$page.props.data.current_page + 2"
                        @click="this.params.page = $page.props.data.current_page + 2"
                        v-if="$page.props.data.last_page >= $page.props.data.current_page + 2"
                        :variant="'clear'"
                    />
                    <Button
                        :text="$page.props.data.last_page"
                        @click="this.params.page = $page.props.data.last_page"
                        v-if="$page.props.data.last_page > $page.props.data.current_page + 2"
                        :variant="'clear'"
                    />
                </div>
                <Button
                    :icon="'iconoir-nav-arrow-right'"
                    :variant="'primary'"
                    @click="this.params.page < $page.props.data.last_page ? (this.params.page = this.params.page + 1) : null"
                />
            </div>
        </div>
    </MainLayout>
    <AddNewProductModal
        v-model:show="this.modal.addNewProductModal"
        :product_categories="$page.props.product_categories"
        :product_units="$page.props.product_units"
    />
    <ViewProductModal v-model:data="this.modal.viewProductModal" />
    <EditProductModal
        v-model:data="this.modal.editProductModal"
        :product_categories="$page.props.product_categories"
        :product_units="$page.props.product_units"
    />
</template>

<style lang="scss" scoped>
@use "./../../../css/app.scss";
.content {
    display: flex;
    flex-direction: column;
    row-gap: 1rem;

    &__header {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        row-gap: 2rem;
        column-gap: 5rem;
    }

    &__title {
        font-size: 1.5rem;
        font-weight: 500;
        color: var(--color-1);
    }
}

.actions {
    display: flex;
    justify-content: space-between;
    align-items: stretch;
    flex-wrap: wrap;
    row-gap: 1rem;
    column-gap: 5rem;

    &__left {
        display: flex;
        column-gap: 1rem;
    }
}
.search {
    display: flex;
    align-items: stretch;
    column-gap: 1rem;
    color: var(--color-1);
    width: 100%;

    @include app.screen(sm) {
        width: fit-content;
    }

    &__main {
        border: 1px solid var(--color-4);
        border-radius: 4px;
        background-color: var(--color-5);
        width: 100%;
        padding: 1rem 1.5rem;
        position: relative;

        @include app.screen(sm) {
            max-width: 20rem;
        }
    }

    &__input {
        all: unset;
    }

    &__clear {
        all: unset;
        cursor: pointer;
        top: 0;
        right: 0;
        position: absolute;
        height: 100%;
        padding-inline: 1rem;
        display: flex;
        align-items: center;
    }

    &__clear-icon {
        font-size: 1.2rem;
    }
}

.total {
    color: var(--color-3);
}

.pagination {
    display: flex;
    column-gap: 0.5rem;

    &__number {
        display: flex;
        background-color: var(--color-5);
        border-radius: 4px;
        border: 1px solid var(--color-4);
    }
}

.select {
    border-radius: 4px;
    border: 1px solid var(--color-4);
    background-color: var(--color-5);
    cursor: pointer;
    color: var(--color-1);
    position: relative;
    display: flex;
    align-items: center;
    height: 100%;

    &__icon {
        padding: 1rem;
        position: absolute;
        pointer-events: none;
    }

    &__menu {
        all: unset;
        padding: 1rem;
        padding-left: 3rem;
    }
}
</style>
