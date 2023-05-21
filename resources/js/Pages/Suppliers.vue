<script>
import { Link, router } from "@inertiajs/vue3";
import axios from "axios";
import MainLayout from "@/Layouts/MainLayout.vue";
import Button from "@/Components/Button.vue";
import AddNewSupplierModal from "../Components/AddNewSupplierModal.vue";
import ViewSupplierModal from "../Components/ViewSupplierModal.vue";
import EditSupplierModal from "../Components/EditSupplierModal.vue";

export default {
    layout: MainLayout,
    components: {
        Button,
        AddNewSupplierModal,
        ViewSupplierModal,
        Link,
        EditSupplierModal,
    },
    data() {
        return {
            params: {
                page: 1,
                search: "",
                sortBy: "name",
            },
            modal: {
                addNewSupplierModal: false,
                viewSupplierModal: {
                    show: false,
                    id: null,
                    data: {},
                },
                editSupplierModal: {
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
            router.delete(`/suppliers/${id}`);
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
            this.selected.forEach((id) => {
                router.delete(`/suppliers/${id}`);
                this.selected = [];
                this.selectAll = false;
            });
        },
    },
    watch: {
        "modal.viewSupplierModal.id": async function () {
            if (this.modal.viewSupplierModal.id !== null) {
                const { data } = await axios.get(
                    `/suppliers/${this.modal.viewSupplierModal.id}`
                );
                this.modal.viewSupplierModal.data = data;
                this.modal.viewSupplierModal.show = true;
            } else {
                this.modal.viewSupplierModal.show = false;
            }
        },
        "modal.editSupplierModal.id": async function () {
            if (this.modal.editSupplierModal.id !== null) {
                const { data } = await axios.get(
                    `/suppliers/${this.modal.editSupplierModal.id}`
                );
                this.modal.editSupplierModal.data = data;
                this.modal.editSupplierModal.show = true;
            } else {
                this.modal.editSupplierModal.show = false;
            }
        },
        params: {
            handler() {
                router.visit(
                    `/suppliers?page=${this.params.page}&search=${this.params.search}&sort=${this.params.sortBy}`,
                    {
                        only: ["data"],
                        preserveState: true,
                    }
                );
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
    <MainLayout>
        <div class="content">
            <div class="content__row content__row--between">
                <h1 class="content__title">Suppliers</h1>
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
                        :text="'Search: ' + this.params.search"
                        :icon="'iconoir-cancel'"
                        v-if="this.params.search"
                        @click="this.params.search = ''"
                    />
                </div>
                <div class="actions__right">
                    <Button
                        :text="'Add new supplier'"
                        :icon="'iconoir-plus'"
                        :variant="'primary'"
                        @click="this.modal.addNewSupplierModal = true"
                    />
                </div>
            </div>
            <table class="table">
                <thead class="table__head table__row">
                    <th class="table__cell">
                        <input type="checkbox" v-model="this.selectAll" />
                    </th>
                    <th class="table__cell">NAME</th>
                    <th class="table__cell">PHONE</th>
                    <th class="table__cell">DESCRIPTION</th>
                    <th class="table__cell">ACTIONS</th>
                </thead>

                <tbody class="table__body">
                    <tr
                        class="table__row"
                        v-for="item in $page.props.data.data"
                    >
                        <td class="table__cell" :key="item.id">
                            <input
                                type="checkbox"
                                :value="item.id"
                                v-model="this.selected"
                            />
                        </td>
                        <td class="table__cell">{{ item.name }}</td>
                        <td class="table__cell">{{ item.phone }}</td>
                        <td class="table__cell">{{ item.description }}</td>
                        <td class="table__cell">
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
                                            this.modal.viewSupplierModal.id =
                                                item.id
                                        "
                                    />
                                    <Button
                                        :text="'Edit'"
                                        :variant="'clear'"
                                        class="item-action__link"
                                        @click="
                                            this.modal.editSupplierModal.id =
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
                </tbody>
            </table>
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
    <AddNewSupplierModal
        :show="this.modal.addNewSupplierModal"
        @close="this.modal.addNewSupplierModal = false"
    />
    <ViewSupplierModal
        :show="this.modal.viewSupplierModal.show"
        :data="this.modal.viewSupplierModal.data"
        @close="this.modal.viewSupplierModal.id = null"
    />
    <EditSupplierModal
        :show="this.modal.editSupplierModal.show"
        :data="this.modal.editSupplierModal.data"
        @close="this.modal.editSupplierModal.id = null"
    />
</template>

<style lang="scss" scoped>
@use "./../../css/app";
.content {
    display: flex;
    flex-direction: column;
    row-gap: 1rem;

    &__row {
        display: flex;

        &--between {
            justify-content: space-between;
        }
    }

    &__title {
        font-size: 1.5rem;
        font-weight: 500;
        color: var(--color-1);
    }
}
.table {
    @include app.table;
}

.actions {
    display: flex;
    justify-content: space-between;
    align-items: center;

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

    &__main {
        border: 1px solid var(--color-4);
        border-radius: 4px;
        background-color: var(--color-5);
        min-width: 20rem;
        padding: 1rem 1.5rem;
        position: relative;
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

    &__icon {
        padding: 1rem;
        font-size: 1.2rem;
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
