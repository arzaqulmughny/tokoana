<script setup>
import Button from "@/Components/Button.vue";
import Table from "@/Components/Table.vue";

const props = defineProps({
    data: Object,
});

const emit = defineEmits(["close", "print"]);

function dateFormatter(time) {
    const date = new Date(time);
    return `${date.getFullYear()}-${date.getMonth()}-${date.getDate()} at ${date.getHours()}:${date.getMinutes()} ${
        date.getHours() >= 12 ? "PM" : "AM"
    }`;
}

const close = () => {
    emit("close");
};

const printAndClose = () => {
    emit("print");
    emit("close");
};
</script>
<template>
    <Teleport to="#modals">
        <div class="overlay" v-if="props.data !== null">
            <div class="modal">
                <div class="modal__header">
                    <div class="modal__head-left">
                        <i class="iconoir-add-square modal__icon"></i>
                    </div>
                    <div class="modal__head-right">
                        <div class="modal__head-text">
                            <h1 class="modal__title">Sale detail</h1>
                            <h2 class="modal__subtitle">
                                Detail information about this sale
                            </h2>
                        </div>
                        <Button
                            class="modal__close"
                            :icon="'iconoir-cancel'"
                            :variant="'secondary'"
                            @click="close"
                        />
                    </div>
                </div>

                <div class="content">
                    <table class="table-vertical">
                        <tr class="table-vertical__row">
                            <th class="table-vertical__head">INVOICE ID</th>
                            <td
                                class="table-vertical__data"
                                v-html="props.data.id"
                            />
                        </tr>
                        <tr class="table-vertical__row">
                            <th class="table-vertical__head">CASHIER</th>
                            <td
                                class="table-vertical__data"
                                v-html="props.data.user.name"
                            />
                        </tr>
                        <tr class="table-vertical__row">
                            <th class="table-vertical__head">DATE & TIME</th>
                            <td
                                class="table-vertical__data"
                                v-html="dateFormatter(props.data.created_at)"
                            />
                        </tr>
                        <tr class="table-vertical__row">
                            <th class="table-vertical__head">AMOUNT</th>
                            <td
                                class="table-vertical__data"
                                v-html="props.data.amount"
                            />
                        </tr>
                        <tr class="table-vertical__row">
                            <th class="table-vertical__head">CASH</th>
                            <td
                                class="table-vertical__data"
                                v-html="props.data.cash"
                            />
                        </tr>
                        <tr class="table-vertical__row">
                            <th class="table-vertical__head">CHANGE</th>
                            <td
                                class="table-vertical__data"
                                v-html="props.data.change"
                            />
                        </tr>
                        <tr class="table-vertical__row">
                            <th class="table-vertical__head">TOTAL ITEMS</th>
                            <td
                                class="table-vertical__data"
                                v-html="props.data.items.length"
                            />
                        </tr>
                    </table>

                    <Table>
                        <template #head>
                            <tr>
                                <td>BARCODE</td>
                                <td>NAME</td>
                                <td>CURRENT PRICE</td>
                                <td>QUANTITY</td>
                                <td>AMOUNT</td>
                            </tr>
                        </template>
                        <template #body>
                            <tr v-for="item in props.data.items">
                                <td v-html="item.detail.barcode" />
                                <td v-html="item.detail.name" />
                                <td v-html="item.current_price" />
                                <td v-html="item.quantity" />
                                <td v-html="item.quantity * item.amount" />
                            </tr>
                        </template>
                    </Table>

                    <div class="content__action">
                        <Button
                            :text="'Close'"
                            :variant="'secondary'"
                            @click="close"
                        />
                        <Button
                            :text="'Print receipt'"
                            :variant="'primary'"
                            @click="printAndClose"
                        />
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style lang="scss" scoped>
.table-vertical {
    color: var(--color-1);

    &__head {
        text-align: start;
        font-weight: 500;
        padding-block: 1rem;
        padding-right: 2rem;
    }

    &__data {
        color: var(--color-3);
    }
}
.overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-color: rgba($color: #000000, $alpha: 0.4);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal {
    background-color: var(--color-5);
    border-radius: 4px;
    border: 1px solid var(--color-3);
    max-height: 80vh;
    overflow-y: auto;
    max-width: 80rem;
    position: relative;

    &__header {
        top: 0;
        background-color: var(--color-5);
        position: sticky;
        display: flex;
        column-gap: 1rem;
        padding: 1.5rem;
        border-bottom: 1px solid var(--color-4);
    }

    &__icon {
        font-size: 1.5rem;
        color: var(--color-1);
    }

    &__title {
        font-size: 1.2rem;
        color: var(--color-1);
    }

    &__subtitle {
        color: var(--color-3);
    }

    &__head-text {
        display: flex;
        flex-direction: column;
        row-gap: 0.5rem;
    }

    &__head-right {
        display: flex;
        column-gap: 3rem;
        width: 100%;
        justify-content: space-between;
    }
}

.content {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    row-gap: 2rem;

    &__action {
        display: flex;
        justify-content: space-between;
    }

    &__between {
        display: flex;
        justify-content: space-between;
        column-gap: 1rem;
        align-items: flex-start;
    }
}
</style>
