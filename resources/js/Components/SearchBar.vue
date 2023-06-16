<script setup>
import Button from "@/Components/Button.vue";

defineProps(["value", "name", "placeholder"]);
defineEmits(["search", "update:value"]);
</script>

<template>
    <form class="search" @submit.prevent="$emit('update:value', value)">
        <div class="search__main">
            <input
                type="text"
                class="search__input"
                :name="name"
                :placeholder="placeholder"
                autocomplete="off"
                :value="value"
                @input="$emit('update:value', $event.target.value)"
            />
            <button class="search__clear" v-if="value.length > 0" @click="$emit('update:value', '')">
                <i class="iconoir-cancel search__clear-icon"></i>
            </button>
        </div>
        <Button :type="'submit'" :icon="'iconoir-search'" :variant="'primary'" />
    </form>
</template>

<style lang="scss" scoped>
@use "./../../css/app.scss";
.search {
    display: flex;
    align-items: stretch;
    column-gap: 1rem;
    color: var(--color-1);
    width: 100%;

    @include app.screen(sm) {
        width: fit-content;
    }

    &__main {
        border: 1px solid var(--color-4);
        border-radius: 4px;
        background-color: var(--color-5);
        width: 100%;
        padding: 1rem 1.5rem;
        position: relative;

        @include app.screen(sm) {
            max-width: 20rem;
        }
    }

    &__input {
        all: unset;
    }

    &__clear {
        all: unset;
        cursor: pointer;
        top: 0;
        right: 0;
        position: absolute;
        height: 100%;
        padding-inline: 1rem;
        display: flex;
        align-items: center;
    }

    &__clear-icon {
        font-size: 1.2rem;
    }
}
</style>
