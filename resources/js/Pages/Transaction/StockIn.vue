<script>
export default {
    layout: Mainlayout,
    data() {
        return {
            params: {
                search: "",
                page: ""
            },
            addedProduct: {
                data: [],
                sortBy: "oldest"
            },
            selected: [],
            selectAll: false,
            formData: {
                supplier: "",
                supplier_id: "",
                note: ""
            },
            errors: {
                supplier_id: "",
                note: ""
            }
        };
    },
    methods: {
        addProduct(item) {
            const index = this.addedProduct.data.findIndex(itemInArray => itemInArray.id == item.id);
            if (index > -1) {
                this.addedProduct.data[index].quantity += 1;
            } else {
                item.quantity = 1;
                item.addedAt = Date.now();
                this.addedProduct.data.push(item);
            }
        },
        removeProduct(id) {
            this.addedProduct.data = this.addedProduct.data.filter(itemInArray => itemInArray.id != id);
        },
        removeSelected() {
            this.addedProduct.data.forEach(item => this.removeProduct(item.id));
            this.selectAll = false;
            this.selected = [];
        },
        adjustProductQuantity(id, action) {
            const index = this.addedProduct.data.findIndex(item => item.id == id);
            switch (action) {
                case "decrease":
                    this.addedProduct.data[index].quantity > 1 ? (this.addedProduct.data[index].quantity -= 1) : this.removeProduct(id);

                    break;
                case "increase":
                    this.addedProduct.data[index].quantity += 1;
                    break;
                default:
                    return;
            }
        },
        async submit() {
            // Form validate
            this.errors = {
                supplier_id: "",
                note: ""
            };

            if (this.addedProduct.data.length === 0) {
                alert("No products to be added!");
                return;
            }

            if (this.formData.supplier_id.length === 0 && this.formData.note.length === 0) {
                this.errors.note = "Please provide a note if supplier not selected.";
            }

            if (!confirm("Submit this data?")) {
                return;
            }
            try {
                const postStockInHistoryResponse = await this.postStockInHistory({
                    ...this.formData,
                    user_id: this.$page.props.user.id
                });

                const main = async postStockInHistoryResponseId => {
                    try {
                        this.addedProduct.data.forEach(async product => {
                            await this.postStockInItem({
                                product_id: product.id,
                                history_id: postStockInHistoryResponse.id,
                                ...product
                            });
                            await this.putProductListStock({
                                id: product.id,
                                quantity: product.quantity
                            });
                        });
                        alert("Successfully added data!");
                        this.clearData();
                    } catch (error) {
                        await this.deleteStockInHistory(postStockInHistoryResponseId);
                        alert("Failed to add data!");
                    }
                };
                main(postStockInHistoryResponse.id);
            } catch (error) {
                alert("Failed to add data!");
            }
        },
        clearData(showConfirm = false) {
            if (showConfirm === "yes") {
                if (!confirm("Confirm clear current data?")) {
                    return;
                }
            }

            this.params = {
                search: "",
                page: 1
            };
            this.selected = [];
            this.selectAll = false;
            this.formData = {
                supplier_id: "",
                note: ""
            };
            this.addedProduct = {
                data: [],
                sortBy: "oldest"
            };
        },
        async postStockInHistory({ supplier_id, note, user_id }) {
            const { data } = await axios.post("/transaction/in/", {
                supplier_id,
                note,
                user_id
            });
            return data;
        },
        async postStockInItem({ product_id, barcode, unit_id, quantity, history_id }) {
            const { data } = await axios.post("/transaction/in/items", {
                product_id,
                barcode,
                unit_id,
                quantity,
                history_id
            });
            return data;
        },
        async putProductListStock({ id, quantity }) {
            const getCurrentProductStockResponse = await this.getCurrentProductStock(id);
            const { data } = await axios.put(`/product/list/${id}`, {
                stock: getCurrentProductStockResponse + quantity
            });
            return data;
        },
        async getCurrentProductStock(id) {
            const { data } = await axios.get(`/product/list/${id}`);
            return data.stock;
        },
        async deleteStockInHistory(id) {
            const { data } = await axios.delete(`/transaction/in/${id}`);
            return data;
        }
    },
    computed: {
        getAddedProduct() {
            switch (this.addedProduct.sortBy) {
                case "name":
                    return this.addedProduct.data.sort((a, b) => a.name.localeCompare(b.name));
                case "oldest":
                    return this.addedProduct.data.sort((a, b) => a.addedAt - b.addedAt);
                case "latest":
                    return this.addedProduct.data.sort((a, b) => b.addedAt - a.addedAt);
                default:
                    return this.addedProduct.data;
            }
        },
        getAddedProductStockTotal() {
            let total = 0;
            this.addedProduct.data.forEach(item => {
                total = total + item.quantity;
            });
            return total;
        },
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
        },
        selectAll() {
            if (this.selectAll) {
                this.addedProduct.data.forEach(item => {
                    this.selected.push(item.id);
                });
            } else {
                this.selected = [];
            }
        }
    }
};
</script>

<script setup>
import { router } from "@inertiajs/vue3";
import Mainlayout from "@/Layouts/MainLayout.vue";
import Button from "@/Components/Button.vue";
import Table from "@/Components/Table.vue";
import axios from "axios";
import { Head } from "@inertiajs/vue3";
import SelectCustom from "@/Components/SelectCustom.vue";
import SearchBar from "@/Components/SearchBar.vue";
</script>

<template>
    <Head>
        <title>Stock In</title>
    </Head>
    <MainLayout>
        <div class="main">
            <div class="main__left">
                <div class="content">
                    <h1 class="content__title">Product List</h1>
                    <div class="content__actions">
                        <SearchBar :placeholder="'Search product...'" :name="'search'" v-model:value="this.params.search" />
                    </div>
                    <Table>
                        <template #head>
                            <tr>
                                <td>BARCODE</td>
                                <td>NAME</td>
                                <td>CATEGORY</td>
                                <td>UNIT</td>
                                <td>ACTIONS</td>
                            </tr>
                        </template>
                        <template #body>
                            <tr data-empty="true" v-if="$page.props.data.data.length == 0">
                                <td colspan="100">No items available</td>
                            </tr>
                            <tr v-for="item in $page.props.data.data">
                                <td v-html="item.barcode" />
                                <td v-html="item.name" />
                                <td v-html="item.category.name" />
                                <td v-html="item.unit.name" />
                                <td>
                                    <Button @click="this.addProduct(item)" :text="'+ Add'" :variant="'primary'" :size="'small'" />
                                </td>
                            </tr>
                        </template>
                    </Table>
                    <div class="pagination">
                        <Button
                            :icon="'iconoir-nav-arrow-left'"
                            :variant="'secondary'"
                            @click="this.params.page > 1 ? (this.params.page = this.params.page - 1) : null"
                        />

                        <div class="pagination__number">
                            <Button
                                :text="1"
                                :variant="1 == $page.props.data.current_page ? 'primary' : 'clear'"
                                @click="this.params.page = 1"
                            />
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
                                v-if="$page.props.data.last_page > $page.props.data.current_page + 2"
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
                <div class="content">
                    <h1 class="content__title">Added products</h1>
                    <div class="content__actions">
                        <SelectCustom :text="'Sort'" :variant="'secondary'" :icon="'iconoir-sort'" :menuPosition="'right'">
                            <span class="select-custom__option" @click="this.addedProduct.sortBy = 'name'" v-text="'Name'" />
                            <span class="select-custom__option" @click="this.addedProduct.sortBy = 'latest'" v-text="'Last added'" />
                            <span class="select-custom__option" @click="this.addedProduct.sortBy = 'oldest'" v-text="'Added first'" />
                        </SelectCustom>
                        <Button
                            :text="`Remove selected (${this.selected.length})`"
                            :icon="'iconoir-cancel'"
                            v-if="this.selected.length"
                            @click="this.removeSelected"
                            :variant="'secondary'"
                        />
                    </div>
                    <Table>
                        <template #head>
                            <tr>
                                <td>
                                    <input type="checkbox" v-model="this.selectAll" />
                                </td>
                                <td>BARCODE</td>
                                <td>NAME</td>
                                <td>QUANTITY</td>
                                <td>UNIT</td>
                                <td>ACTIONS</td>
                            </tr>
                        </template>
                        <template #body>
                            <tr data-empty="true" v-if="this.addedProduct.data.length == 0">
                                <td colspan="100">No items available</td>
                            </tr>
                            <tr v-for="item in this.getAddedProduct">
                                <td>
                                    <input type="checkbox" :value="item.id" v-model="this.selected" />
                                </td>
                                <td v-html="item.barcode" />
                                <td v-html="item.name" />
                                <td>
                                    <div class="quantity">
                                        <Button
                                            @click="this.adjustProductQuantity(item.id, 'decrease')"
                                            :icon="'iconoir-minus'"
                                            :variant="'secondary'"
                                            :size="'small'"
                                        />
                                        <span v-html="item.quantity" />
                                        <Button
                                            @click="this.adjustProductQuantity(item.id, 'increase')"
                                            :variant="'primary'"
                                            :icon="'iconoir-plus'"
                                            :size="'small'"
                                        />
                                    </div>
                                </td>
                                <td v-html="item.unit.name" />
                                <td @click="removeProduct(item.id)">
                                    <Button :text="'Remove'" :variant="'secondary'" :size="'small'" />
                                </td>
                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
            <form class="main__right" @submit.prevent="this.submit">
                <div class="summary">
                    <div class="summary__header">
                        <i class="summary__icon iconoir-add-square"></i>
                        <h2 class="summary__title">Summary</h2>
                    </div>
                    <div class="summary__body">
                        <div class="summary__property">
                            <small class="summary__key">Total products</small>
                            <div class="small summary__value" v-html="this.addedProduct.data.length" />
                        </div>

                        <div class="summary__property">
                            <small class="summary__key">Total stock</small>
                            <div class="small summary__value" v-html="this.getAddedProductStockTotal" />
                        </div>
                    </div>
                </div>

                <div class="description">
                    <SelectCustom
                        :text="this.formData.supplier || 'Select supplier'"
                        :icon="'iconoir-truck'"
                        :variant="'secondary'"
                        :menuPosition="'right'"
                    >
                        <span
                            v-for="item in $page.props.suppliers"
                            class="select-custom__option"
                            @click="
                                () => {
                                    this.formData.supplier = item.name;
                                    this.formData.supplier_id = item.id;
                                }
                            "
                            v-text="item.name"
                        />
                    </SelectCustom>

                    <div class="note">
                        <label for="note" class="note__title">Note</label>
                        <textarea name="note" id="note" cols="30" rows="10" class="note__main" v-model="this.formData.note"></textarea>
                        <small class="note__invalid" v-if="this.errors.note" v-html="this.errors.note" />
                    </div>
                </div>

                <div class="actions">
                    <Button :text="'Confirm'" :variant="'primary'" :type="'submit'" />
                    <Button :text="'Clear data'" :variant="'secondary'" @click="this.clearData('yes')" />
                </div>
            </form>
        </div>
    </MainLayout>
</template>

<style lang="scss" scoped>
@use "./../../../css/app.scss";

.main {
    display: flex;
    flex-direction: row;
    gap: 2rem;
    flex-wrap: wrap;
    justify-content: space-between;

    &__left {
        display: flex;
        flex-direction: column;
        row-gap: 2rem;
        width: 100%;

        @include app.screen(lg) {
            max-width: 55%;
        }
    }

    &__right {
        display: flex;
        flex-direction: column;
        row-gap: 2rem;
        width: 100%;

        @include app.screen(lg) {
            max-width: fit-content;
            min-width: fit-content;
        }
    }
}

.content {
    display: flex;
    flex-direction: column;
    row-gap: 1rem;

    &__title {
        font-size: 1.5rem;
        font-weight: 500;
        color: var(--color-1);
    }

    &__actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
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

.summary {
    width: 100%;
    background-color: var(--color-5);
    border-radius: 4px;
    border: 1px solid var(--color-4);

    &__header {
        display: flex;
        column-gap: 1rem;
        font-size: 1.4rem;
        align-items: center;
        border-bottom: 1px solid var(--color-4);
        padding: 2rem;
    }

    &__icon {
        font-size: 2rem;
        color: var(--color-1);
    }

    &__title {
        color: var(--color-1);
    }

    &__body {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        row-gap: 1rem;
    }

    &__property {
        display: flex;
        justify-content: space-between;
        color: var(--color-3);
    }
}

.description {
    background-color: var(--color-5);
    border-radius: 4px;
    border: 1px solid var(--color-4);
    padding: 2rem;
    display: flex;
    row-gap: 2rem;
    flex-direction: column;
}

.note {
    display: flex;
    flex-direction: column;
    row-gap: 1rem;

    &__title {
        color: var(--color-1);
    }

    &__main {
        resize: none;
        border: 1px solid var(--color-4);
        outline: none;
        padding: 1rem;
        font-family: "Inter", sans-serif;
        color: var(--color-1);
    }

    &__invalid {
        color: red;
        line-height: 1.5rem;
    }
}

.actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;

    & > button {
        justify-content: center;
        width: 100%;
    }
}

.quantity {
    display: flex;
    column-gap: 1rem;
}
</style>
