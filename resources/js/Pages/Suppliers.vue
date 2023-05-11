<script>
import MainLayout from "@/Layouts/MainLayout.vue";
import Search from "@/Components/Search.vue";
import Button from "@/Components/Button.vue";
import { router } from "@inertiajs/vue3";
import AddNewSupplierModalVue from "../Components/AddNewSupplierModal.vue";

export default {
    layout: MainLayout,
    components: {
        Search,
        Button,
        AddNewSupplierModalVue,
    },
    data() {
        return {
            modal: {
                addNewSupplierModalVue: false,
            },
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
        removeItem(id) {
            router.delete(`/suppliers/${id}`);
        },
    },
};
</script>

<template>
    <MainLayout>
        <div class="content">
            <div class="content__row content__row--between">
                <h1 class="content__title">Suppliers</h1>
                <Search />
            </div>
            <div class="actions">
                <div class="actions__left">
                    <Button
                        :text="'Sort by'"
                        :icon="'iconoir-sort'"
                        :variant="'secondary'"
                    />
                    <Button
                        :text="'Filter'"
                        :icon="'iconoir-filter'"
                        :variant="'secondary'"
                    />
                    <Button
                        :text="'Remove selected (1)'"
                        :icon="'iconoir-cancel'"
                        :variant="'secondary'"
                    />
                </div>
                <div class="actions__right">
                    <Button
                        :text="'Add new supplier'"
                        :icon="'iconoir-plus'"
                        :variant="'primary'"
                        @click="this.modal.addNewSupplierModalVue = true"
                    />
                </div>
            </div>
            <table class="table">
                <thead class="table__head table__row">
                    <th class="table__cell">
                        <input type="checkbox" name="" id="" />
                    </th>
                    <th class="table__cell">NAME</th>
                    <th class="table__cell">PHONE</th>
                    <th class="table__cell">DESCRIPTION</th>
                    <th class="table__cell">ACTIONS</th>
                </thead>

                <tbody class="table__body">
                    <tr class="table__row" v-for="item in $page.props.data">
                        <td class="table__cell" :key="item.id">
                            <input type="checkbox" name="" id="" />
                        </td>
                        <td class="table__cell">{{ item.name }}</td>
                        <td class="table__cell">{{ item.phone }}</td>
                        <td class="table__cell">{{ item.description }}</td>
                        <td class="table__cell">
                            <div class="item-action">
                                <Button
                                    :icon="'iconoir-nav-arrow-down'"
                                    @click="toggleItemAction"
                                />
                                <div class="item-action__list">
                                    <Button
                                        :text="'View'"
                                        :variant="'clear'"
                                        class="item-action__link"
                                    />
                                    <Button
                                        :text="'Edit'"
                                        :variant="'clear'"
                                        class="item-action__link"
                                    />
                                    <Button
                                        :text="'Remove'"
                                        :variant="'clear'"
                                        class="item-action__link"
                                        @click="removeItem(item.id)"
                                    />
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </MainLayout>
    <AddNewSupplierModalVue
        :show="this.modal.addNewSupplierModalVue"
        @close="this.modal.addNewSupplierModalVue = false"
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
    position: relative;
    width: fit-content;

    &__list {
        border-radius: 4px;
        position: absolute;
        display: none;
        top: calc(100% + 1rem);
        right: 0;
        background-color: var(--color-5);
        z-index: 9;
        border: 1px solid var(--color-4);

        &--active {
            display: flex;
            flex-direction: column;
        }
    }
}
</style>
