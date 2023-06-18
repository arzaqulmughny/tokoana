<script setup>
import Button from "@/Components/Button.vue";
import Table from "@/Components/Table.vue";
import dateFormatter from "@/Utils/dateFormatter";

defineProps(["data"]);
const emits = defineEmits(["update:data", "print"]);

const printAndClose = id => {
    emits("update:data", null);
    emits("print", id);
};
</script>
<template>
    <Teleport to="#modals">
        <div class="overlay" v-if="data !== null">
            <div class="modal">
                <div class="modal__header">
                    <div class="modal__head-left">
                        <i class="iconoir-add-square modal__icon"></i>
                    </div>
                    <div class="modal__head-right">
                        <div class="modal__head-text">
                            <h1 class="modal__title">Stock Out detail</h1>
                            <h2 class="modal__subtitle">Detail information about this transaction</h2>
                        </div>
                        <Button class="modal__close" :icon="'iconoir-cancel'" :variant="'secondary'" @click="$emit('update:data', null)" />
                    </div>
                </div>

                <div class="content">
                    <table class="table-vertical">
                        <tr class="table-vertical__row">
                            <th class="table-vertical__head">USER</th>
                            <td class="table-vertical__data" v-html="data.user.name" />
                        </tr>
                        <tr class="table-vertical__row">
                            <th class="table-vertical__head">TOTAL ITEMS</th>
                            <td class="table-vertical__data" v-html="data.items.length" />
                        </tr>
                        <tr class="table-vertical__row">
                            <th class="table-vertical__head">NOTE</th>
                            <td class="table-vertical__data" v-html="data.note" />
                        </tr>
                        <tr class="table-vertical__row">
                            <th class="table-vertical__head">DATE & TIME</th>
                            <td class="table-vertical__data" v-html="dateFormatter(data.created_at)" />
                        </tr>
                    </table>

                    <Table>
                        <template #head>
                            <tr>
                                <td>BARCODE</td>
                                <td>NAME</td>
                                <td>QUANTITY</td>
                                <td>UNIT</td>
                                <td>CATEGORY</td>
                            </tr>
                        </template>
                        <template #body>
                            <tr v-for="item in data.items">
                                <td v-html="item.details.barcode" />
                                <td v-html="item.details.name" />
                                <td v-html="item.quantity" />
                                <td v-html="item.details.unit.name" />
                                <td v-html="item.details.category.name" />
                            </tr>
                        </template>
                    </Table>

                    <div class="content__action">
                        <Button :text="'Close'" :variant="'secondary'" @click="$emit('update:data', null)" />
                        <Button :text="'Print receipt'" :variant="'primary'" @click="printAndClose(data.id)" />
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
