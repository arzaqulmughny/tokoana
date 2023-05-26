<script setup>
import Button from "@/Components/Button.vue";
import Input from "./Input.vue";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    show: Boolean,
    product_categories: Array,
    product_units: Array,
});

const emit = defineEmits(["close"]);
const formData = ref({
    name: "",
    unit_id: "",
    category_id: "",
    barcode: "",
    price: "",
});

const close = () => {
    emit("close");
};

const submit = () => {
    router.post("/product/list", formData.value, {
        onSuccess: () => {
            close();
            formData.value = {
                name: "",
                unit_id: 1,
                category_id: 1,
                barcode: "",
                price: "",
            };
        },
    });
};
</script>
<template>
    <Teleport to="#modals">
        <div class="overlay" v-if="props.show === true">
            <div class="modal">
                <div class="modal__header">
                    <div class="modal__head-left">
                        <i class="iconoir-add-square modal__icon"></i>
                    </div>
                    <div class="modal__head-right">
                        <div class="modal__head-text">
                            <h1 class="modal__title">Add new product</h1>
                            <h2 class="modal__subtitle">
                                Fill the data below to add new product
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

                <form class="form" @submit.prevent="submit">
                    <Input
                        :name="'name'"
                        :displayName="'Product name'"
                        :icon="'iconoir-box-iso'"
                        :type="'text'"
                        :placeholder="'Product name'"
                        :value="formData.name"
                        @update="(newValue) => (formData.name = newValue)"
                        :error="$page.props.errors.name"
                    />

                    <div class="form__between">
                        <div class="select">
                            <label for="unit" class="select__label">Unit</label>
                            <div class="select__body">
                                <select
                                    name="unit_id"
                                    id="unit"
                                    class="select__main"
                                    v-model="formData.unit_id"
                                >
                                    <option
                                        class="select__option"
                                        disabled
                                        value=""
                                    >
                                        Select unit
                                    </option>
                                    <option
                                        class="select__option"
                                        v-for="item in props.product_units"
                                        :value="item.id"
                                        v-html="item.name"
                                    />
                                </select>

                                <i
                                    class="iconoir-nav-arrow-down select__icon"
                                ></i>
                            </div>
                            <small
                                class="select__invalid"
                                v-if="$page.props.errors.unit_id"
                                v-html="'Unit is required.'"
                            />
                        </div>

                        <div class="select">
                            <label for="category" class="select__label"
                                >Category</label
                            >
                            <div class="select__body">
                                <select
                                    name="category_id"
                                    id="category"
                                    class="select__main"
                                    v-model="formData.category_id"
                                >
                                    <option
                                        class="select__option"
                                        disabled
                                        value=""
                                    >
                                        Select category
                                    </option>
                                    <option
                                        class="select__option"
                                        v-for="item in props.product_categories"
                                        :value="item.id"
                                        v-html="item.name"
                                    />
                                </select>
                                <i
                                    class="iconoir-nav-arrow-down select__icon"
                                ></i>
                            </div>
                            <small
                                class="select__invalid"
                                v-if="$page.props.errors.category_id"
                                v-html="'Category is required.'"
                            />
                        </div>
                    </div>

                    <Input
                        :name="'barcode'"
                        :displayName="'Barcode'"
                        :icon="'iconoir-barcode'"
                        :type="'text'"
                        :placeholder="'Barcode'"
                        :value="formData.barcode"
                        @update="(newValue) => (formData.barcode = newValue)"
                        :error="$page.props.errors.barcode"
                    />

                    <Input
                        :name="'price'"
                        :displayName="'Price'"
                        :icon="'iconoir-money-square'"
                        :type="'text'"
                        :placeholder="'Price'"
                        :value="formData.price"
                        @update="(newValue) => (formData.price = newValue)"
                        :error="$page.props.errors.price"
                    />

                    <div class="form__action">
                        <Button
                            :text="'Cancel'"
                            :variant="'secondary'"
                            @click="close"
                        />
                        <Button
                            :text="'Add'"
                            :variant="'primary'"
                            :icon="'iconoir-plus'"
                            :type="'submit'"
                        />
                    </div>
                </form>
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
    max-height: 90vh;
    overflow-y: auto;

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

.select {
    display: flex;
    flex-direction: column;
    row-gap: 1rem;
    overflow: hidden;
    flex: 1;

    &__label {
        color: var(--color-1);
    }

    &__main {
        -webkit-appearance: none;
        background-color: var(--color-5);
        border: 1px solid var(--color-4);
        border-radius: 4px;
        padding: 1rem 2rem;
        padding-right: 4rem;
        width: 100%;
        cursor: pointer;
        color: var(--color-1);
        outline: none;
    }

    &__body {
        position: relative;
        border-radius: 4px;
    }

    &__icon {
        position: absolute;
        right: 0;
        top: 0;
        padding: 1rem 2rem;
        font-size: 1.2rem;
        color: var(--color-1);
        pointer-events: none;
    }

    &__invalid {
        color: red;
    }
}
</style>
