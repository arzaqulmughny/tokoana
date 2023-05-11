<script setup>
const props = defineProps({
    icon: String,
    text: String,
    variant: String,
});
</script>

<template>
    <button
        class="button"
        :class="'button--' + props.variant"
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

    &__icon {
        font-size: 1.2rem;
    }

    &--primary {
        background-color: var(--color-2);
        color: var(--color-5);
    }

    &--secondary {
        background-color: var(--color-5);
        color: var(--color-1);
    }
}
</style>
