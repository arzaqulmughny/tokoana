<script setup>
import { onMounted, onUnmounted, ref } from "vue";

const show = ref(false);
const main = ref(null);
const menu = ref(null);

let handler = event => {
    if (event.target === main.value) {
        show.value = !show.value;
    } else {
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
    <div class="item-action" ref="main">
        <i class="iconoir-nav-arrow-down item-action__arrow" />
        <div class="item-action__list" v-if="show" ref="menu">
            <slot />
        </div>
    </div>
</template>

<style lang="scss">
.item-action {
    display: flex;
    position: relative;
    width: fit-content;
    cursor: pointer;

    &__arrow {
        pointer-events: none;
    }

    &__checkbox {
        all: unset;
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
    }

    &__list {
        border-radius: 4px;
        position: absolute;
        display: flex;
        top: calc(100% + 1rem);
        right: 0;
        background-color: var(--color-5);
        z-index: 9;
        border: 1px solid var(--color-4);
        flex-direction: column;
    }
}
</style>
