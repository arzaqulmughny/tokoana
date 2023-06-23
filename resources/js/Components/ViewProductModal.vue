<script setup>
import Button from "@/Components/Button.vue";
import Input from "./Input.vue";

defineProps(["data"]);
defineEmits(["update:data"]);
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
                            <h1 class="modal__title">Product detail</h1>
                            <h2 class="modal__subtitle">Detail information about this product</h2>
                        </div>
                        <Button class="modal__close" :icon="'iconoir-cancel'" :variant="'secondary'" @click="$emit('update:data', null)" />
                    </div>
                </div>

                <div class="form">
                    <Input :displayName="'Product name'" :icon="'iconoir-box-iso'" :type="'text'" :value="data.name" :disabled="true" />

                    <div class="form__between">
                        <Input :displayName="'Unit'" :type="'text'" :value="data.unit.name" :disabled="true" />

                        <Input :displayName="'Category'" :type="'text'" :value="data.category.name" :disabled="true" />
                    </div>

                    <Input :displayName="'Barcode'" :icon="'iconoir-barcode'" :type="'text'" :value="data.barcode" :disabled="true" />

                    <Input :displayName="'Price'" :icon="'iconoir-money-square'" :type="'text'" :value="data.price" :disabled="true" />

                    <div class="form__action">
                        <Button :text="'Close'" :variant="'secondary'" @click="$emit('update:data', null)" />
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style lang="scss" scoped>
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
    height: fit-content;
    max-width: 30rem;

    &__header {
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

.form {
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
