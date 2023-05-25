<script setup>
import Button from "@/Components/Button.vue";
import Input from "./Input.vue";
import { watch, ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    show: Boolean,
    data: Object,
});

const emit = defineEmits(["close"]);

const close = () => {
    emit("close");
};

const formData = ref({
    name: "",
});

watch(
    () => props.data,
    () => {
        formData.value.name = props.data.name;
    }
);
const submit = () => {
    if (confirm("Save changes?")) {
        router.put(`/product/units/${props.data.id}`, formData.value, {
            onSuccess: () => {
                close();
                formData.value = {
                    name: "",
                };
            },
        });
    }
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
                            <h1 class="modal__title">Edit product unit</h1>
                            <h2 class="modal__subtitle">
                                Edit this product unit information
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
                        :displayName="'Product unit name'"
                        :icon="'iconoir-diameter'"
                        :type="'text'"
                        :value="formData.name"
                        @update="(newValue) => (formData.name = newValue)"
                        :error="$page.props.errors.name"
                    />

                    <div class="form__action">
                        <Button
                            :text="'Close'"
                            :variant="'secondary'"
                            @click="close"
                        />
                        <Button
                            :text="'Save changes'"
                            :variant="'primary'"
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
    height: fit-content;

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
}
</style>
