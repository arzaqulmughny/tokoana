<script>
import { Link, router } from "@inertiajs/vue3";
import axios from "axios";
import { Head } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import Button from "@/Components/Button.vue";
import AddNewProductModal from "../../Components/AddNewProductModal.vue";
import ViewProductModal from "../../Components/ViewProductModal.vue";
import EditProductModal from "../../Components/EditProductModal.vue";
import Table from "@/Components/Table.vue";

export default {
    layout: MainLayout,
    components: {
        Button,
        AddNewProductModal,
        ViewProductModal,
        Link,
        EditProductModal,
        Head,
        Table,
    },
    data() {
        return {
            params: {
                page: 1,
                search: "",
                sortBy: "name",
            },
            modal: {
                addNewProductModal: false,
                viewProductModal: {
                    show: false,
                    id: null,
                    data: {},
                },
                editProductModal: {
                    show: false,
                    id: null,
                    data: {},
                },
            },
            selectAll: false,
            selected: [],
        };
    },
    methods: {
        toggleItemAction(event) {
            Array.from(
                document.getElementsByClassName("item-action__list--active")
            ).forEach((element) => {
                if (event.target.nextElementSibling != element) {
                    element.classList.toggle("item-action__list--active");
                }
            });

            event.target.nextElementSibling.classList.toggle(
                "item-action__list--active"
            );
        },
        removeItem(event, id) {
            event.target.parentElement.parentElement.children[1].checked = false;
            if (confirm("Delete this item?")) {
                router.delete(`/product/list/${id}`);
            }
        },
        blur(event) {
            if (
                event.relatedTarget === null ||
                !event.relatedTarget.className.includes("item-action__link")
            ) {
                event.target.checked = false;
            }
        },
        deleteSelected() {
            if (confirm("Delete selected item?")) {
                this.selected.forEach((id) => {
                    router.delete(`/product/list/${id}`);
                    this.selected = [];
                    this.selectAll = false;
                });
            }
        },
    },
    watch: {
        "modal.viewProductModal.id": async function () {
            if (this.modal.viewProductModal.id !== null) {
                const { data } = await axios.get(
                    `/product/list/${this.modal.viewProductModal.id}`
                );
                this.modal.viewProductModal.data = data;
                this.modal.viewProductModal.show = true;
            } else {
                this.modal.viewProductModal.show = false;
            }
        },
        "modal.editProductModal.id": async function () {
            if (this.modal.editProductModal.id !== null) {
                const { data } = await axios.get(
                    `/product/list/${this.modal.editProductModal.id}`
                );
                this.modal.editProductModal.data = data;
                this.modal.editProductModal.show = true;
            } else {
                this.modal.editProductModal.show = false;
            }
        },
        params: {
            handler() {
                if (this.timer) {
                    clearTimeout(this.timer);
                }
                this.timer = setTimeout(() => {
                    router.visit(
                        `/product/list?page=${this.params.page}&search=${this.params.search}&sort=${this.params.sortBy}`,
                        {
                            only: ["data"],
                            preserveState: true,
                            preserveScroll: true,
                        }
                    );
                }, 1000);
            },
            deep: true,
        },
        selectAll() {
            if (this.selectAll) {
                this.$page.props.data.data.forEach((item) => {
                    this.selected.push(item.id);
                });
            } else {
                this.selected = [];
            }
        },
        searchAndSort() {
            this.params.page = 1;
        },
    },
    computed: {
        searchAndSort() {
            return `${this.params.search}|${this.params.sortBy}`;
        },
    },
    created() {
        let url = window.location.href;
        url = new URL(url);

        url.searchParams.get("page")
            ? (this.params.page = url.searchParams.get("page"))
            : null;
        url.searchParams.get("search")
            ? (this.params.search = url.searchParams.get("search"))
            : null;
        url.searchParams.get("sort")
            ? (this.params.sortBy = url.searchParams.get("sort"))
            : null;
    },
};
</script>

<template>
    <Head>
        <title>Product List</title>
    </Head>
    <MainLayout>
        <div class="content">
            <div class="content__header">
                <h1 class="content__title">Product List</h1>
                <form class="search" @submit.prevent="search">
                    <div class="search__main">
                        <input
                            type="text"
                            class="search__input"
                            name="search"
                            placeholder="Search..."
                            autocomplete="off"
                            v-model="this.params.search"
                        />
                        <button
                            class="search__clear"
                            v-if="this.params.search"
                            @click="this.params.search = ''"
                        >
                            <i class="iconoir-cancel search__clear-icon"></i>
                        </button>
                    </div>
                    <Button
                        :type="'submit'"
                        :icon="'iconoir-search'"
                        :variant="'primary'"
                    />
                </form>
            </div>
            <div class="actions">
                <div class="actions__left">
                    <div class="select">
                        <i class="iconoir-sort select__icon"></i>
                        <select
                            class="select__menu"
                            v-model="this.params.sortBy"
                        >
                            <option value="name">Name</option>
                            <option value="latest">Latest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>
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
                    <Button
                        :text="'Add new product'"
                        :icon="'iconoir-plus'"
                        :variant="'primary'"
                        @click="this.modal.addNewProductModal = true"
                    />
                </div>
            </div>
            <Table>
                <template #head>
                    <tr>
                        <td>
                            <input
                                type="checkbox"
                                v-model="this.selectAll"
                                class="table__select"
                            />
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
                    <tr
                        data-empty="true"
                        v-if="$page.props.data.data.length == 0"
                    >
                        <td colspan="100">No items available</td>
                    </tr>
                    <tr v-for="item in $page.props.data.data">
                        <td :key="item.id">
                            <input
                                type="checkbox"
                                :value="item.id"
                                v-model="this.selected"
                            />
                        </td>
                        <td>{{ item.barcode || "" }}</td>
                        <td>{{ item.name || "" }}</td>
                        <td>{{ item.category.name || "" }}</td>
                        <td>{{ item.unit.name || "" }}</td>
                        <td>{{ item.price || "" }}</td>
                        <td>{{ item.stock }}</td>
                        <td>
                            <div class="item-action">
                                <Button :icon="'iconoir-nav-arrow-down'" />
                                <input
                                    type="checkbox"
                                    class="item-action__checkbox"
                                    @blur="blur"
                                />
                                <div class="item-action__list">
                                    <Button
                                        :text="'View'"
                                        :variant="'clear'"
                                        class="item-action__link"
                                        @click="
                                            this.modal.viewProductModal.id =
                                                item.id
                                        "
                                    />
                                    <Button
                                        :text="'Edit'"
                                        :variant="'clear'"
                                        class="item-action__link"
                                        @click="
                                            this.modal.editProductModal.id =
                                                item.id
                                        "
                                    />
                                    <Button
                                        :text="'Remove'"
                                        :variant="'clear'"
                                        class="item-action__link"
                                        @click="
                                            (event) =>
                                                removeItem(event, item.id)
                                        "
                                    />
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
            <span class="total"
                >{{ $page.props.data.total }} total items available</span
            >
            <div class="pagination">
                <Button
                    :icon="'iconoir-nav-arrow-left'"
                    :variant="'secondary'"
                    @click="
                        this.params.page > 1
                            ? (this.params.page = this.params.page - 1)
                            : null
                    "
                />

                <div class="pagination__number">
                    <Button
                        :text="1"
                        :variant="
                            1 == $page.props.data.current_page
                                ? 'primary'
                                : 'clear'
                        "
                        @click="this.params.page = 1"
                    />
                    <Button
                        v-if="$page.props.data.current_page != 1"
                        :text="$page.props.data.current_page"
                        :variant="'primary'"
                    />
                    <Button
                        :text="$page.props.data.current_page + 1"
                        @click="
                            this.params.page = $page.props.data.current_page + 1
                        "
                        v-if="
                            $page.props.data.last_page >=
                            $page.props.data.current_page + 1
                        "
                        :variant="'clear'"
                    />
                    <Button
                        :text="$page.props.data.current_page + 2"
                        @click="
                            this.params.page = $page.props.data.current_page + 2
                        "
                        v-if="
                            $page.props.data.last_page >=
                            $page.props.data.current_page + 2
                        "
                        :variant="'clear'"
                    />
                    <Button
                        :text="$page.props.data.last_page"
                        @click="this.params.page = $page.props.data.last_page"
                        v-if="
                            $page.props.data.last_page >=
                            $page.props.data.current_page + 2
                        "
                        :variant="'clear'"
                    />
                </div>
                <Button
                    :icon="'iconoir-nav-arrow-right'"
                    :variant="'primary'"
                    @click="
                        this.params.page < $page.props.data.last_page
                            ? (this.params.page = this.params.page + 1)
                            : null
                    "
                />
            </div>
        </div>
    </MainLayout>
    <AddNewProductModal
        :show="this.modal.addNewProductModal"
        :product_categories="$page.props.product_categories"
        :product_units="$page.props.product_units"
        @close="this.modal.addNewProductModal = false"
    />
    <ViewProductModal
        :show="this.modal.viewProductModal.show"
        :data="this.modal.viewProductModal.data"
        @close="this.modal.viewProductModal.id = null"
    />
    <EditProductModal
        :show="this.modal.editProductModal.show"
        :data="this.modal.editProductModal.data"
        @close="this.modal.editProductModal.id = null"
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
.item-action {
    display: flex;
    visibility: hidden;
    position: relative;
    width: fit-content;

    &__checkbox {
        all: unset;
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
    }

    &__checkbox:checked {
        ~ .item-action__list {
            display: flex;
        }
    }

    &__list {
        border-radius: 4px;
        position: absolute;
        display: none;
        top: calc(100% + 1rem);
        right: 0;
        background-color: var(--color-5);
        z-index: 9;
        border: 1px solid var(--color-4);
        flex-direction: column;

        &--active {
            display: flex;
        }
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
