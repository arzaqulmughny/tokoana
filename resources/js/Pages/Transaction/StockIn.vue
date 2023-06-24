<script>
import { router } from "@inertiajs/vue3";
import Mainlayout from "@/Layouts/MainLayout.vue";
import Button from "@/Components/Button.vue";
import Table from "@/Components/Table.vue";
import axios from "axios";
import { Head } from "@inertiajs/vue3";

export default {
    layout: Mainlayout,
    components: {
        Button,
        Table,
        Head
    },
    data() {
        return {
            params: {
                search: "",
                page: 1
            },
            addedProduct: {
                data: [],
                sortBy: "oldest"
            },
            selected: [],
            selectAll: false,
            formData: {
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
        }
    },
    watch: {
        params: {
            handler() {
                if (this.timer) {
                    clearTimeout(this.timer);
                }
                this.timer = setTimeout(() => {
                    router.visit(`/transaction/in?page=${this.params.page}&search=${this.params.search}`, {
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
                        <form class="search">
                            <div class="search__main">
                                <input
                                    type="text"
                                    class="search__input"
                                    name="search"
                                    placeholder="Search..."
                                    autocomplete="off"
                                    v-model="this.params.search"
                                />
                                <button class="search__clear" v-if="this.params.search" @click="this.params.search = ''">
                                    <i class="iconoir-cancel search__clear-icon"></i>
                                </button>
                            </div>
                            <Button :type="'submit'" :icon="'iconoir-search'" :variant="'primary'" />
                        </form>
                        <Button :text="'Show all'" :variant="'secondary'" @click="this.params.search = ''" />
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
                        <div class="select">
                            <i class="iconoir-sort select__icon"></i>
                            <select class="select__menu" v-model="this.addedProduct.sortBy">
                                <option value="name">Name</option>
                                <option value="latest">Last Added</option>
                                <option value="oldest">Added first</option>
                            </select>
                        </div>
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
                    <div class="select-supplier">
                        <label for="supplier" class="select-supplier__label">Supplier</label>
                        <div class="select-supplier__container">
                            <i class="select-supplier__icon iconoir-truck" />
                            <select name="supplier_id" class="select-supplier__main" v-model="this.formData.supplier_id">
                                <option value="" disable v-html="'Select supplier'" />
                                <option v-for="item in $page.props.suppliers" :value="item.id" v-html="item.name" />
                            </select>
                            <i class="select-supplier__icon iconoir-nav-arrow-down"></i>
                        </div>
                    </div>

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
    }

    &__right {
        display: flex;
        flex-direction: column;
        row-gap: 2rem;
        max-width: 25rem;
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
        font-size: 1.2rem;
    }

    &__menu {
        all: unset;
        padding: 1rem;
        padding-left: 3rem;
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

.select-supplier {
    display: flex;
    flex-direction: column;
    row-gap: 1rem;

    &__label {
        color: var(--color-1);
    }

    &__container {
        width: 100%;
        position: relative;
        padding-block: 1.2rem;
        display: flex;
        padding-inline: 2rem;
        justify-content: space-between;
    }

    &__main {
        position: absolute;
        width: 100%;
        height: 100%;
        padding-left: 5rem;
        left: 0;
        top: 0;
        -webkit-appearance: none;
        background-color: var(--color-5);
        cursor: pointer;
        outline: none;
        border: 1px solid var(--color-4);
        border-radius: 4px;
        color: var(--color-1);
    }

    &__icon {
        font-size: 1.2rem;
        z-index: 9;
        pointer-events: none;
        color: var(--color-1);
    }
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
    flex-direction: column;
    row-gap: 1rem;

    & > button {
        justify-content: center;
    }
}

.quantity {
    display: flex;
    column-gap: 1rem;
}
</style>
