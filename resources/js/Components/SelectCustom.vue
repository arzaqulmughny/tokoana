<script setup>
import { onMounted, onUnmounted, ref } from "vue";
defineProps(["icon", "text", "variant", "menuPosition"]);

const show = ref(false);
const main = ref(null);
const menu = ref(null);

const handler = event => {
    if (event.target === main.value) {
        show.value = !show.value;
    }

    if (!main.value.contains(event.target)) {
        show.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handler);
});

onUnmounted(() => {
    document.removeEventListener("click", handler);
});
</script>

<template>
    <div class="select-custom" :class="'select-custom--' + variant" ref="main">
        <i class="select-custom__icon" :class="icon" />
        <span class="select-custom__text" v-html="text" />
        <i class="iconoir-nav-arrow-down select-custom__icon" />
        <div class="select-custom__menu" :class="'select-custom__menu--' + menuPosition" v-if="show" ref="menu">
            <slot />
        </div>
    </div>
</template>

<style lang="scss">
.select-custom {
    display: flex;
    position: relative;
    align-items: center;
    border: 1px solid rgba($color: #000000, $alpha: 0.1) !important;
    border-radius: 4px;
    padding: 1rem !important;
    column-gap: 1rem;
    cursor: pointer;
    justify-content: space-between;

    &__icon {
        pointer-events: none;
    }

    &__text {
        pointer-events: none;
    }

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
