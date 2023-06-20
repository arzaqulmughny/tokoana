<script>
import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfMake.vfs = pdfFonts.pdfMake.vfs;

export default {
    layout: MainLayout,
    data() {
        return {
            params: {
                search: "",
                sort: "",
                page: ""
            },
            modal: {
                viewEmployee: null,
                addNewEmployee: false,
                editEmployee: null,
                editEmployeePassword: null
            }
        };
    },
    methods: {
        async getDetailData(id) {
            const { data } = await axios.get(`/employee/${id}`);
            return data;
        },
        removeData(id) {
            if (!confirm("Remove this user?")) {
                return;
            }

            try {
                router.delete(`employee/${id}`);
                this.params.search = "";
            } catch (e) {
                alert("Failed to remove data!");
            }
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
import { Head, router } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import SearchBar from "@/Components/SearchBar.vue";
import Table from "@/Components/Table.vue";
import Button from "@/Components/Button.vue";
import dateFormatter from "@/Utils/dateFormatter";
import SelectCustom from "@/Components/SelectCustom.vue";
import RowMenu from "@/Components/RowMenu.vue";
import ViewEmployeeModal from "@/Components/ViewEmployeeModal.vue";
import AddNewEmployeeModal from "@/Components/AddNewEmployeeModal.vue";
import EditEmployeeModal from "@/Components/EditEmployeeModal.vue";
import EditEmployeePasswordModal from "@/Components/EditEmployeePasswordModal.vue";
</script>

<template>
    <Head>
        <title>Employee</title>
    </Head>
    <div class="main">
        <div class="main__header">
            <h1 class="main__title">Employee</h1>
            <Button :variant="'primary'" :text="'Add new employee'" :icon="'iconoir-plus'" @click="this.modal.addNewEmployee = true" />
        </div>
        <div class="main__actions">
            <div class="main__actions-left">
                <SelectCustom :text="'Sort'" :icon="'iconoir-sort'" :variant="'secondary'" :menuPosition="'right'">
                    <span class="select-custom__option" @click="this.params.sort = 'latest'">Latest</span>
                    <span class="select-custom__option" @click="this.params.sort = 'oldest'">Oldest</span>
                </SelectCustom>
            </div>
            <SearchBar :name="'search'" :placeholder="'Search by name or username...'" v-model:value="this.params.search" />
        </div>
        <div class="main__body">
            <Table>
                <template #head>
                    <tr>
                        <td>USERNAME</td>
                        <td>NAME</td>
                        <td>CREATED AT</td>
                        <td>ACTIONS</td>
                    </tr>
                </template>
                <template #body>
                    <tr data-empty="true" v-if="$page.props.data.data.length == 0">
                        <td colspan="100">No items available</td>
                    </tr>
                    <tr v-for="item in this.$page.props.data.data">
                        <td v-html="item.username" />
                        <td v-html="item.name" />
                        <td v-html="dateFormatter(item.created_at)" />
                        <td>
                            <RowMenu>
                                <Button
                                    :text="'View'"
                                    class="item-action__link"
                                    :variant="'clear'"
                                    @click="async () => (this.modal.viewEmployee = await this.getDetailData(item.id))"
                                />
                                <Button
                                    :text="'Edit'"
                                    class="item-action__link"
                                    :variant="'clear'"
                                    @click="async () => (this.modal.editEmployee = await this.getDetailData(item.id))"
                                />
                                <Button
                                    :text="'Update password'"
                                    class="item-action__link"
                                    :variant="'clear'"
                                    @click="async () => (this.modal.editEmployeePassword = await this.getDetailData(item.id))"
                                />
                                <Button :text="'Remove'" class="item-action__link" :variant="'clear'" @click="this.removeData(item.id)" />
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
    </div>
    <ViewEmployeeModal v-model:data="this.modal.viewEmployee" />
    <AddNewEmployeeModal v-model:show="this.modal.addNewEmployee" />
    <EditEmployeeModal v-model:data="this.modal.editEmployee" />
    <EditEmployeePasswordModal v-model:data="this.modal.editEmployeePassword" />
</template>

<style lang="scss" scoped>
@use "./../../css/app.scss";
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

.total {
    color: var(--color-3);
}
.main {
    padding: 0;
    &__header {
        display: flex;
        justify-content: space-between;
    }

    &__actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    &__actions-left {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    &__title {
        font-size: 1.5rem;
        font-weight: 500;
        color: var(--color-1);
    }

    &__body {
        display: flex;
        flex-direction: column;
        row-gap: 1rem;
    }
}
</style>
