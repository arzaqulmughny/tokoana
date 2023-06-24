<script setup>
const props = defineProps({
    icon: String,
    text: String | Number,
    variant: String,
    type: String,
    size: String
});
</script>

<template>
    <button
        class="button"
        :class="[[props.variant && `button--${props.variant}`], [props.size && `button--${props.size}`]]"
        :type="props.type || 'button'"
    >
        <i class="button__icon" :class="props.icon" v-if="props.icon"></i>
        <span class="button__text" v-if="props.text">{{ props.text }}</span>
    </button>
</template>

<style lang="scss" scoped>
.button {
    all: unset;
    cursor: pointer;
    border-radius: 4px;
    border: 1px solid rgba($color: #000000, $alpha: 0.1);
    display: flex;
    align-items: center;
    column-gap: 1rem;
    padding: 1rem;
    width: fit-content;

    &:active {
        transform: translateY(-1px);
    }

    &__icon {
        pointer-events: none;
        font-size: 1.2rem;
    }

    &__text {
        pointer-events: none;
    }

    &--clear {
        border: none;
    }

    &--primary {
        background-color: var(--color-2);
        color: var(--color-5);

        &:hover {
            background-color: var(--color-2-hover);
        }
    }

    &--secondary {
        background-color: var(--color-5);
        color: var(--color-1);

        &:hover {
            background-color: var(--color-5-hover);
        }
    }

    &--small {
        padding: 2px 4px;
    }
}
</style>
