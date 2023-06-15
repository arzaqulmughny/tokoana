<script setup>
defineProps(["icon", "text", "variant", "menuPosition"]);
</script>

<template>
    <div class="select-custom" :class="'select-custom--' + variant">
        <i class="select-custom__icon" :class="icon" />
        <span class="select-custom__text" v-html="text" />
        <i class="iconoir-nav-arrow-down select-custom__icon" />
        <input type="checkbox" class="select-custom__checkbox" />
        <div class="select-custom__menu" :class="'select-custom__menu--' + menuPosition">
            <slot name="menu" />
        </div>
    </div>
</template>

<style lang="scss">
.select-custom {
    display: flex;
    position: relative;
    align-items: center;
    border: 1px solid rgba($color: #000000, $alpha: 0.1);
    border-radius: 4px;
    padding: 1rem !important;
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
        padding: 1rem !important;
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
        padding: 1rem !important;
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
</style>
