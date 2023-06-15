<script>
import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfMake.vfs = pdfFonts.pdfMake.vfs;

export default {
    layout: MainLayout,
    data() {
        return {
            params: {
                cashier: "",
                sort: "",
                page: "",
                from: "",
                to: ""
            },
            tempRange: {
                from: "",
                to: ""
            },
            modal: {
                viewSaleHistory: null
            },
            tempPrintRange: {
                from: "",
                to: ""
            }
        };
    },
    methods: {
        async getDetailData(id) {
            const { data } = await axios.get(`/history/sales/${id}`);
            this.modal.viewSaleHistory = data;
        },
        async printReceipt(id) {
            const { data } = await axios.get(`/history/sales/${id}`);
            const items = data.items.map((item, index) => {
                return {
                    layout: "noBorders",
                    table: {
                        headerRows: 1,
                        widths: ["auto", "auto"],
                        body: [
                            [`[${index + 1}]`, item.detail.name],
                            ["", `${item.quantity} X ${item.current_price} = ${item.amount}`]
                        ]
                    }
                };
            });
            const docDefinition = {
                pageSize: { width: 283.46, height: "auto" },
                content: [
                    { text: "TOKOANA", bold: true, fontSize: 14 },
                    { text: "POINT OF SALES APP" },
                    {
                        text: "-------------------------------------------------------------------------"
                    },
                    { text: `#${data.id}` },
                    { text: dateFormatter(data.created_at) },
                    {
                        columns: [{ text: "CASHIER" }, { text: data.user.name, alignment: "right" }]
                    },
                    {
                        text: "-------------------------------------------------------------------------"
                    },
                    ...items,
                    {
                        text: "-------------------------------------------------------------------------"
                    },
                    {
                        columns: [
                            { text: "SUBTOTAL", bold: true },
                            {
                                text: data.amount,
                                alignment: "right",
                                bold: true
                            }
                        ]
                    },
                    {
                        columns: [
                            { text: "TOTAL", bold: true },
                            {
                                text: data.amount,
                                alignment: "right",
                                bold: true
                            }
                        ]
                    },
                    {
                        columns: [
                            { text: "CASH", bold: true },
                            { text: data.cash, alignment: "right", bold: true }
                        ]
                    },
                    {
                        columns: [
                            { text: "CHANGE", bold: true },
                            {
                                text: data.change,
                                alignment: "right",
                                bold: true
                            }
                        ]
                    },
                    {
                        text: "-------------------------------------------------------------------------"
                    },
                    {
                        text: "THANK YOU FOR PURCHASING!",
                        alignment: "center"
                    }
                ],
                defaultStyle: {
                    fontSise: 12
                },
                pageMargins: [20, 20, 20, 20]
            };
            pdfMake.createPdf(docDefinition, null).open();
        },
        async printRangedData(from, to = Date.now()) {
            const { data } = await axios.get(`/history/sales?from=${Math.floor(from / 1000)}&to=${Math.floor(to / 1000)}&mode=print`);
            if (data.length === 0) {
                alert("No data found in this date!");
                return;
            }

            let earned = 0;
            data.forEach(item => (earned += item.amount));
            let items = data.map(sale => [sale.id, sale.user.name, sale.amount, sale.cash, sale.change, dateFormatter(sale.created_at)]);

            const docDefinition = {
                pageSize: "A4",
                pageOrientation: "landscape",
                pageMargins: [50, 50, 50, 50],
                defaultStyle: {
                    fontSise: 12,
                    lineHeight: 1.4
                },
                images: {
                    logo: {
                        url: `http://${window.location.host}/assets/images/logo.png`
                    }
                },
                content: [
                    {
                        columns: [
                            {
                                width: "*",
                                columns: [
                                    {
                                        image: `logo`,
                                        width: 58
                                    },
                                    {
                                        layout: "noBorders",
                                        table: {
                                            headerRows: 1,
                                            widths: ["auto"],
                                            body: [[{ text: "TOKOANA", fontSize: 18, bold: true }], ["Point of Sales App"]]
                                        }
                                    }
                                ]
                            },
                            {
                                width: "auto",
                                layout: "noBorders",
                                table: {
                                    headerRows: 1,
                                    widths: [100, "auto"],
                                    body: [
                                        [
                                            "Date",
                                            {
                                                text: `${dateFormatter(from, false)} - ${dateFormatter(to, false)}`,
                                                color: "#5D5D5D"
                                            }
                                        ],
                                        ["Printed on", { text: `${dateFormatter(Date.now(), false)}`, color: "#5D5D5D" }],
                                        ["Printed by", { text: this.$page.props.user.name, color: "#5D5D5D" }]
                                    ]
                                }
                            }
                        ],
                        columnGap: 10
                    },
                    {
                        canvas: [{ type: "line", x1: 0, y1: 20, x2: 730, y2: 20, lineWidth: 0.1, lineColor: "#A9A9A9" }]
                    },
                    {
                        text: "\nSales History Report",
                        fontSize: 18,
                        bold: true
                    },
                    {
                        layout: "noBorders",
                        table: {
                            headerRows: 1,
                            widths: ["auto", "auto"],
                            body: [
                                ["Total sales", { text: data.length, color: "#5D5D5D" }],
                                ["Total earned", { text: earned, color: "#5D5D5D" }]
                            ]
                        }
                    },
                    {
                        text: "\n"
                    },
                    {
                        layout: "custom1",
                        table: {
                            headerRows: 1,
                            widths: ["auto", "*", "*", "*", "*", "*"],
                            body: [["INVOICE ID", "CASHIER", "AMOUNT", "CASH", "CHANGE", "TIME"], ...items]
                        }
                    }
                ]
            };

            const tableLayouts = {
                custom1: {
                    hLineWidth: function () {
                        return 1;
                    },
                    vLineWidth: function () {
                        return 1;
                    },
                    hLineColor: function () {
                        return "#A9A9A9";
                    },
                    vLineColor: function () {
                        return "#A9A9A9";
                    },
                    paddingLeft: function () {
                        return 5;
                    },
                    paddingRight: function () {
                        return 5;
                    },
                    paddingTop: function () {
                        return 5;
                    },
                    paddingBottom: function () {
                        return 5;
                    }
                }
            };
            pdfMake.createPdf(docDefinition, tableLayouts).open();
        },
        printSelectedRangeData() {
            if (this.tempPrintRange.from.length === 0 || this.tempPrintRange.to.length === 0) {
                alert("Enter valid date!");
                return;
            }
            this.printRangedData(new Date(this.tempPrintRange.from), new Date(this.tempPrintRange.to));
        },
        applyDateRange() {
            if (this.tempRange.from.length === 0 && this.tempRange.to.length === 0) {
                return;
            }

            this.params.from = new Date(this.tempRange.from).getTime() / 1000;
            this.params.to = new Date(this.tempRange.to).getTime() / 1000;
        },
        showPassedDaysData(days) {
            this.params.from = Math.floor(new Date().getTime() / 1000.0) - days * 24 * 60 * 60;
            this.params.to = Math.floor(new Date().getTime() / 1000.0);
        },
        toggleItemAction(event) {
            Array.from(document.getElementsByClassName("item-action__list--active")).forEach(element => {
                if (event.target.nextElementSibling != element) {
                    element.classList.toggle("item-action__list--active");
                }
            });

            event.target.nextElementSibling.classList.toggle("item-action__list--active");
        },
        blur(event) {
            if (event.relatedTarget === null || !event.relatedTarget.className.includes("item-action__link")) {
                event.target.checked = false;
            }
        },
        resetDate() {
            this.params.from = "";
            this.params.to = "";
            console.log("tes");
        }
    },
    computed: {
        getUrlWithParams() {
            let URL = "/history/sales?";
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
import Input from "@/Components/Input.vue";
import ViewSaleHistoryModal from "@/Components/ViewSaleHistoryModal.vue";
import dateFormatter from "@/Utils/dateFormatter";
import SelectCustom from "@/Components/SelectCustom.vue";
</script>

<template>
    <Head>
        <title>Sales History</title>
    </Head>
    <div class="main">
        <div class="main__header">
            <h1 class="main__title">Sales History</h1>
            <SelectCustom :text="'Print'" :icon="'iconoir-printing-page'" :variant="'primary'" :menuPosition="'left'">
                <template #menu>
                    <span class="select-custom__option" @click="this.printRangedData(getPreviousDay(undefined, 1))">Today</span>
                    <span class="select-custom__option" @click="this.printRangedData(getPreviousDay(undefined, 7))">Weekly</span>
                    <span class="select-custom__option" @click="this.printRangedData(getPreviousDay(undefined, 1))">Monthly</span>
                    <hr class="select-custom__divider" />
                    <form class="select-custom__form" @submit.prevent="this.printSelectedRangeData">
                        <div class="select-custom__between">
                            <input type="date" class="select-custom__date" v-model="this.tempPrintRange.from" />
                            <span class="select-custom__to">to</span>
                            <input type="date" class="select-custom__date" v-model="this.tempPrintRange.to" />
                        </div>
                        <Button :text="'Apply'" :type="'submit'" :variant="'primary'" />
                    </form>
                </template>
            </SelectCustom>
        </div>
        <div class="main__actions">
            <div class="main__actions-left">
                <SelectCustom :icon="'iconoir-clock-rotate-right'" :text="'Select date'" :variant="'secondary'" :menuPosition="'right'">
                    <template #menu>
                        <span class="select-custom__option" @click="this.showPassedDaysData(1)">Last 24 hours</span>
                        <span class="select-custom__option" @click="this.showPassedDaysData(7)">Last 7 days</span>
                        <span class="select-custom__option" @click="this.showPassedDaysData(30)">Last 30 days</span>
                        <hr class="select-custom__divider" />
                        <form class="select-custom__form" @submit.prevent="this.applyDateRange">
                            <div class="select-custom__between">
                                <input type="date" class="select-custom__date" v-model="this.tempRange.from" />
                                <span class="select-custom__to">to</span>
                                <input type="date" class="select-custom__date" v-model="this.tempRange.to" />
                            </div>
                            <div class="select-custom__actions">
                                <Button :text="'Reset'" :variant="'secondary'" @click="this.resetDate" />
                                <Button :text="'Apply'" :type="'submit'" :variant="'primary'" />
                            </div>
                        </form>
                    </template>
                </SelectCustom>
                <SelectCustom :text="'Sort'" :icon="'iconoir-sort'" :variant="'secondary'" :menuPosition="'right'">
                    <template #menu>
                        <span class="select-custom__option" @click="this.params.sort = 'latest'">Latest</span>
                        <span class="select-custom__option" @click="this.params.sort = 'oldest'">Oldest</span>
                    </template>
                </SelectCustom>
                <Button
                    :text="'Reset date'"
                    :icon="'iconoir-cancel'"
                    v-if="this.params.from.length !== 0 && this.params.to.length !== 0"
                    @click="this.resetDate"
                />
            </div>
            <SearchBar
                :name="'cashier'"
                :placeholder="'Cashier name...'"
                v-model:value="this.params.cashier"
                @submit="this.params = this.params"
            />
        </div>
        <div class="main__body">
            <Table>
                <template #head>
                    <tr>
                        <td>INVOICE ID</td>
                        <td>ITEMS</td>
                        <td>AMOUNT</td>
                        <td>TIME</td>
                        <td>CASHIER</td>
                        <td>ACTIONS</td>
                    </tr>
                </template>
                <template #body>
                    <tr data-empty="true" v-if="$page.props.data.data.length == 0">
                        <td colspan="100">No items available</td>
                    </tr>
                    <tr v-for="item in this.$page.props.data.data">
                        <td v-html="item.id" />
                        <td v-html="item.items.length" />
                        <td v-html="item.amount" />
                        <td v-html="dateFormatter(item.created_at)" />
                        <td v-html="item.user.name" />
                        <td>
                            <div class="item-action">
                                <Button :icon="'iconoir-nav-arrow-down'" />
                                <input type="checkbox" class="item-action__checkbox" @blur="blur" />
                                <div class="item-action__list">
                                    <Button
                                        :text="'View'"
                                        :variant="'clear'"
                                        class="item-action__link"
                                        @click="this.getDetailData(item.id)"
                                    />
                                    <Button
                                        :text="'Print Receipt'"
                                        :variant="'clear'"
                                        class="item-action__link"
                                        @click="this.printReceipt(item.id)"
                                    />
                                </div>
                            </div>
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
    <ViewSaleHistoryModal
        :data="this.modal.viewSaleHistory"
        @close="this.modal.viewSaleHistory = null"
        @print="this.printReceipt(this.modal.viewSaleHistory.id)"
    />
</template>

<style lang="scss" scoped>
@use "../../../css/app.scss";
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
