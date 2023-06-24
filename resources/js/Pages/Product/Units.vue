<script>
import { Link, router } from "@inertiajs/vue3";
import axios from "axios";
import { Head } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import Button from "@/Components/Button.vue";
import AddNewProductUnitModal from "../../Components/AddNewProductUnitModal.vue";
import ViewProductUnitModal from "../../Components/ViewProductUnitModal.vue";
import EditProductUnitModal from "../../Components/EditProductUnitModal.vue";
import Table from "@/Components/Table.vue";

export default {
    layout: MainLayout,
    components: {
        Button,
        AddNewProductUnitModal,
        ViewProductUnitModal,
        Link,
        EditProductUnitModal,
        Head,
        Table
    },
    data() {
        return {
            params: {
                page: 1,
                search: "",
                sortBy: "name"
            },
            modal: {
                addNewProductUnitModal: false,
                viewProductUnitModal: {
                    show: false,
                    id: null,
                    data: {}
                },
                editProductUnitModal: {
                    show: false,
                    id: null,
                    data: {}
                }
            },
            selectAll: false,
            selected: []
        };
    },
    methods: {
        toggleItemAction(event) {
            Array.from(document.getElementsByClassName("item-action__list--active")).forEach(element => {
                if (event.target.nextElementSibling != element) {
                    element.classList.toggle("item-action__list--active");
                }
            });

            event.target.nextElementSibling.classList.toggle("item-action__list--active");
        },
        removeItem(event, id) {
            event.target.parentElement.parentElement.children[1].checked = false;
            if (confirm("Delete this item?")) {
                router.delete(`/product/units/${id}`);
            }
        },
        blur(event) {
            if (event.relatedTarget === null || !event.relatedTarget.className.includes("item-action__link")) {
                event.target.checked = false;
            }
        },
        deleteSelected() {
            if (confirm("Delete selected item?")) {
                this.selected.forEach(id => {
                    router.delete(`/product/units/${id}`);
                    this.selected = [];
                    this.selectAll = false;
                });
            }
        }
    },
    watch: {
        "modal.viewProductUnitModal.id": async function () {
            if (this.modal.viewProductUnitModal.id !== null) {
                const { data } = await axios.get(`/product/units/${this.modal.viewProductUnitModal.id}`);
                this.modal.viewProductUnitModal.data = data;
                this.modal.viewProductUnitModal.show = true;
            } else {
                this.modal.viewProductUnitModal.show = false;
            }
        },
        "modal.editProductUnitModal.id": async function () {
            if (this.modal.editProductUnitModal.id !== null) {
                const { data } = await axios.get(`/product/units/${this.modal.editProductUnitModal.id}`);
                this.modal.editProductUnitModal.data = data;
                this.modal.editProductUnitModal.show = true;
            } else {
                this.modal.editProductUnitModal.show = false;
            }
        },
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
import RowMenu from "@/Components/RowMenu.vue";
import SearchBar from "@/Components/SearchBar.vue";
import SelectCustom from "@/Components/SelectCustom.vue";
</script>

<template>
    <Head>
        <title>Product Units</title>
    </Head>
    <MainLayout>
        <div class="content">
            <div class="content__header">
                <h1 class="content__title">Product Units</h1>
                <Button
                    :text="'Add new product unit'"
                    :icon="'iconoir-plus'"
                    :variant="'primary'"
                    @click="this.modal.addNewProductUnitModal = true"
                    v-if="$page.props.user.user_level === 1"
                />
            </div>
            <div class="actions">
                <div class="actions__left">
                    <SelectCustom :text="'Sort'" :icon="'iconoir-sort'" :variant="'secondary'" :menuPosition="'right'">
                        <span class="select-custom__option" @click="this.params.sort = 'name'">Name</span>
                        <span class="select-custom__option" @click="this.params.sort = 'latest'">Latest</span>
                        <span class="select-custom__option" @click="this.params.sort = 'oldest'">Oldest</span>
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
                    <SearchBar
                        :name="'search'"
                        :placeholder="'Search supplier...'"
                        v-model:value="this.params.search"
                        @submit="this.params = this.params"
                    />
                </div>
            </div>
            <Table>
                <template #head>
                    <tr>
                        <td>
                            <input type="checkbox" v-model="this.selectAll" class="table__select" />
                        </td>
                        <td>NAME</td>
                        <td>ACTIONS</td>
                    </tr>
                </template>
                <template #body>
                    <tr data-empty="true" v-if="$page.props.data.data.length == 0">
                        <td colspan="5">No items available</td>
                    </tr>
                    <tr v-for="item in $page.props.data.data">
                        <td :key="item.id">
                            <input type="checkbox" :value="item.id" v-model="this.selected" />
                        </td>
                        <td>{{ item.name }}</td>
                        <td>
                            <RowMenu>
                                <Button
                                    :text="'View'"
                                    :variant="'clear'"
                                    class="item-action__link"
                                    @click="this.modal.viewProductUnitModal.id = item.id"
                                />
                                <Button
                                    :text="'Edit'"
                                    :variant="'clear'"
                                    class="item-action__link"
                                    @click="this.modal.editProductUnitModal.id = item.id"
                                    v-if="$page.props.user.user_level === 1"
                                />
                                <Button
                                    :text="'Remove'"
                                    :variant="'clear'"
                                    class="item-action__link"
                                    @click="event => removeItem(event, item.id)"
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
                        v-if="$page.props.data.last_page >= $page.props.data.current_page + 2"
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
    <AddNewProductUnitModal :show="this.modal.addNewProductUnitModal" @close="this.modal.addNewProductUnitModal = false" />
    <ViewProductUnitModal
        :show="this.modal.viewProductUnitModal.show"
        :data="this.modal.viewProductUnitModal.data"
        @close="this.modal.viewProductUnitModal.id = null"
    />
    <EditProductUnitModal
        :show="this.modal.editProductUnitModal.show"
        :data="this.modal.editProductUnitModal.data"
        @close="this.modal.editProductUnitModal.id = null"
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
