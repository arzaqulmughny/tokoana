<script>
import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfMake.vfs = pdfFonts.pdfMake.vfs;
import MainLayout from "@/Layouts/MainLayout.vue";
import Table from "@/Components/Table.vue";
import Button from "@/Components/Button.vue";
import { Head, router } from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import ViewSaleHistoryModal from "@/Components/ViewSaleHistoryModal.vue";

export default {
    layout: MainLayout,
    components: {
        Table,
        Head,
        Button,
        Input,
        ViewSaleHistoryModal
    },
    data() {
        return {
            params: {
                sort: "",
                cashier: "",
                page: "",
                from: "",
                to: ""
            },
            print: "",
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
        getPreviousDay(relativeDate = new Date(), days = 1) {
            const previous = new Date(relativeDate.getTime());
            previous.setDate(previous.getDate() - days);
            return previous.getTime();
        },
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
                    { text: this.dateFormatter(data.created_at) },
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

            function formatter(time) {
                const date = new Date(time);
                const result = `${date.getFullYear()}/${date.getMonth()}/${date.getDate()}`;
                return result;
            }

            let earned = 0;
            data.forEach(item => (earned += item.amount));
            let items = data.map(sale => [
                sale.id,
                sale.user.name,
                sale.amount,
                sale.cash,
                sale.change,
                this.dateFormatter(sale.created_at)
            ]);

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
                                        ["Date", { text: `${formatter(from)} - ${formatter(to)}`, color: "#5D5D5D" }],
                                        ["Printed on", { text: `${formatter(Date.now())}`, color: "#5D5D5D" }],
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
        dateFormatter(time) {
            const date = new Date(time);
            return `${date.getFullYear()}-${date.getMonth()}-${date.getDate()} at ${date.getHours()}:${date.getMinutes()} ${
                date.getHours() >= 12 ? "PM" : "AM"
            }`;
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
            2;
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

<template>
    <Head>
        <title>Sales History</title>
    </Head>
    <div class="main">
        <div class="main__header">
            <h1 class="main__title">Sales History</h1>
            <div class="select-custom select-custom--primary">
                <i class="iconoir-printing-page select-custom__icon" />
                <span class="select-custom__text">Print</span>
                <i class="iconoir-nav-arrow-down select-custom__icon" />
                <input type="checkbox" class="select-custom__checkbox" />
                <div class="select-custom__menu select-custom__menu--left">
                    <span class="select-custom__option" @click="this.printRangedData(this.getPreviousDay(undefined, 1))">Today</span>
                    <span class="select-custom__option" @click="this.printRangedData(this.getPreviousDay(undefined, 7))">Weekly</span>
                    <span class="select-custom__option" @click="this.printRangedData(this.getPreviousDay(undefined, 1))">Monthly</span>
                    <hr class="select-custom__divider" />
                    <form class="select-custom__form" @submit.prevent="this.printSelectedRangeData">
                        <div class="select-custom__between">
                            <input type="date" class="select-custom__date" v-model="this.tempPrintRange.from" />
                            <span class="select-custom__to">to</span>
                            <input type="date" class="select-custom__date" v-model="this.tempPrintRange.to" />
                        </div>
                        <Button :text="'Apply'" :type="'submit'" :variant="'primary'" />
                    </form>
                </div>
            </div>
        </div>
        <div class="main__actions">
            <div class="main__actions-left">
                <div class="select-custom select-custom--secondary">
                    <i class="iconoir-clock-rotate-right select-custom__icon" />
                    <span class="select-custom__text">Select time</span>
                    <i class="iconoir-nav-arrow-down select-custom__icon" />
                    <input type="checkbox" class="select-custom__checkbox" />
                    <div class="select-custom__menu select-custom__menu--right">
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
                                <Button :text="'Reset'" :variant="'secondary'" />
                                <Button :text="'Apply'" :type="'submit'" :variant="'primary'" />
                            </div>
                        </form>
                    </div>
                </div>
                <div class="select-custom select-custom--secondary">
                    <i class="iconoir-sort select-custom__icon" />
                    <span class="select-custom__text">Sort</span>
                    <i class="iconoir-nav-arrow-down select-custom__icon" />
                    <input type="checkbox" class="select-custom__checkbox" />
                    <div class="select-custom__menu select-custom__menu--right">
                        <span class="select-custom__option" @click="this.params.sort = 'latest'">Latest</span>
                        <span class="select-custom__option" @click="this.params.sort = 'oldest'">Oldest</span>
                    </div>
                </div>
                <Button
                    :text="'Reset date'"
                    :icon="'iconoir-cancel'"
                    v-if="this.params.from.length !== 0 && this.params.to.length !== 0"
                    @click="this.resetDate"
                />
            </div>
            <form class="search">
                <form class="search" @submit.prevent="search">
                    <div class="search__main">
                        <input
                            type="text"
                            class="search__input"
                            name="cashier"
                            placeholder="Cashier name..."
                            autocomplete="off"
                            v-model="this.params.cashier"
                        />
                        <button class="search__clear" v-if="this.params.cashier" @click="this.params.cashier = ''">
                            <i class="iconoir-cancel search__clear-icon"></i>
                        </button>
                    </div>
                    <Button :type="'submit'" :icon="'iconoir-search'" :variant="'primary'" />
                </form>
            </form>
        </div>
        <div class="main__body">
            <Table>
                <template #head>
                    <tr>
                        <td>#</td>
                        <td>ITEMS</td>
                        <td>AMOUNT</td>
                        <td>TIMESTAMP</td>
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
                        <td v-html="this.dateFormatter(item.created_at)" />
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

.select-custom {
    display: flex;
    position: relative;
    align-items: center;
    border: 1px solid rgba($color: #000000, $alpha: 0.1);
    border-radius: 4px;
    padding: 1rem;
    column-gap: 1rem;
    cursor: pointer;

    &--primary {
        color: var(--color-5);
        background-color: var(--color-2);
    }

    &--primary &__icon {
        color: var(--color-5);
        font-size: 1.2rem;
    }

    &--secondary {
        background-color: var(--color-5);
        color: var(--color-1);
    }

    &--secondary &__icon {
        color: var(--color-1);
        font-size: 1.2rem;
    }

    &__menu {
        position: absolute;
        top: calc(100% + 1rem);
        min-width: 100%;
        display: flex;
        visibility: hidden;
        flex-direction: column;
        background-color: var(--color-5);
        border: 1px solid var(--color-4);
        border-radius: 4px;
        z-index: 1;

        &--left {
            right: -2px;
        }

        &--right {
            left: -2px;
        }
    }

    &__checkbox {
        all: unset;
        left: 0;
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 1;

        &:checked ~ .select-custom__menu {
            visibility: visible;
        }
    }

    &__option {
        padding: 1rem;
        text-align: center;
        color: var(--color-1);

        &:hover {
            background-color: var(--color-bg);
        }
    }

    &__divider {
        width: 100%;
        background-color: var(--color-4);
        height: 1px;
        border: 0;
    }

    &__date {
        color: var(--color-1);
        border: 1px solid var(--color-4);
        padding: 1rem;
        font-family: "Inter", sans-serif;
        outline: none;
        cursor: pointer;
        border-radius: 4px;
    }

    &__to {
        color: var(--color-1);
    }

    &__form {
        display: flex;
        flex-direction: column;
        row-gap: 1rem;
        padding: 1rem;
    }

    &__between {
        display: flex;
        column-gap: 1rem;
        align-items: center;
    }

    &__actions {
        display: flex;
        justify-content: space-between;
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
</style>
