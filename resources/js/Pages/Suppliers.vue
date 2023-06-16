<script>
import axios from "axios";
import { router } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";

export default {
    layout: MainLayout,
    data() {
        return {
            params: {
                page: 1,
                search: "",
                sort: "name"
            },
            modal: {
                addNewSupplierModal: false,
                viewSupplierModal: false,
                editSupplierModal: false
            },
            selectAll: false,
            selected: []
        };
    },
    methods: {
        removeItem(event, id) {
            event.target.parentElement.parentElement.children[1].checked = false;
            if (confirm("Delete this item?")) {
                router.delete(`/suppliers/${id}`);
            }
        },
        deleteSelected() {
            if (confirm("Delete selected item?")) {
                this.selected.forEach(id => {
                    router.delete(`/suppliers/${id}`);
                    this.selected = [];
                    this.selectAll = false;
                });
            }
        },
        async getDetailSupplier(id) {
            const { data } = await axios.get(`/suppliers/${id}`);
            return data;
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

        const params = Object.keys(this.params);

        params.forEach(param => {
            url.searchParams.get(param) && (this.params[param] = url.searchParams.get(param));
        });
    }
};
</script>

<script setup>
import { Head } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import SelectCustom from "@/Components/SelectCustom.vue";
import AddNewSupplierModal from "@/Components/AddNewSupplierModal.vue";
import ViewSupplierModal from "@/Components/ViewSupplierModal.vue";
import EditSupplierModal from "@/Components/EditSupplierModal.vue";
import Table from "@/Components/Table.vue";
import SearchBar from "@/Components/SearchBar.vue";
import RowMenu from "@/Components/RowMenu.vue";
</script>

<template>
    <Head>
        <title>Supplier List</title>
    </Head>
    <MainLayout>
        <div class="content">
            <div class="content__header">
                <h1 class="content__title">Suppliers</h1>
                <Button
                    :text="'Add new supplier'"
                    :icon="'iconoir-plus'"
                    :variant="'primary'"
                    @click="this.modal.addNewSupplierModal = true"
                />
            </div>
            <div class="actions">
                <div class="actions__left">
                    <SelectCustom :text="'Sort'" :icon="'iconoir-sort'" :variant="'secondary'">
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
                        <td>PHONE</td>
                        <td>DESCRIPTION</td>
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
                        <td>{{ item.phone }}</td>
                        <td>{{ item.description }}</td>
                        <td>
                            <RowMenu>
                                <Button
                                    :text="'View'"
                                    :variant="'clear'"
                                    @click="async () => (this.modal.viewSupplierModal = await this.getDetailSupplier(item.id))"
                                />
                                <Button
                                    :text="'Edit'"
                                    :variant="'clear'"
                                    @click="async () => (this.modal.editSupplierModal = await this.getDetailSupplier(item.id))"
                                />
                                <Button :text="'Remove'" :variant="'clear'" @click="event => removeItem(event, item.id)" />
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
    <AddNewSupplierModal v-model:show="this.modal.addNewSupplierModal" />
    <ViewSupplierModal v-model:data="this.modal.viewSupplierModal" />
    <EditSupplierModal v-model:data="this.modal.editSupplierModal" />
</template>

<style lang="scss" scoped>
@use "./../../css/app";
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
</style>
