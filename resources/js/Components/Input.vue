<script setup>
const props = defineProps(["name", "displayName", "icon", "type", "placeholder", "value", "error", "disabled", "tabindex", "autofocus"]);

const emits = defineEmits(["update"]);
</script>
<template>
    <div class="form__item">
        <label class="form__label" for="props.name">{{ props.displayName }}</label>

        <div class="input">
            <i class="input__icon" :class="props.icon" v-if="props.icon"></i>
            <input
                class="input__text"
                :type="props.type"
                :name="props.name"
                :id="props.name"
                :placeholder="props.placeholder"
                :value="props.value"
                @input="emits('update', $event.target.value)"
                autocomplete="off"
                :disabled="props.disabled || false"
                :autofocus="props.autofocus === true"
            />
        </div>
        <small class="input__invalid" v-if="props.error">{{ props.error }}</small>
    </div>
</template>

<style lang="scss" scoped>
.form__item {
    display: flex;
    flex-direction: column;
    row-gap: 1rem;
}
.input {
    border-radius: 4px;
    border: 1px solid var(--color-4);
    display: flex;
    padding: 1rem 2rem;
    column-gap: 25px;

    &__icon {
        font-size: 1.5rem;
        color: var(--color-1);
    }

    &__text {
        all: unset;
        width: 100%;

        &::placeholder {
            color: var(--color-3);
        }
    }

    &__invalid {
        color: red;
    }
}
</style>
