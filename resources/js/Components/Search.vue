<script setup>
    import Button from '@/Components/Button.vue';
</script>

<template>
    <div class="search">
        <input type="text" class="search__input" name="search" placeholder="Search..." autocomplete="off">
        <Button :icon="'iconoir-search'" :variant="'primary'"/>
    </div>
</template>

<style lang="scss" scoped>
   .search {
    display: flex;
        align-items: stretch;
        column-gap: 1rem;
        color: var(--color-1);

        &__input {
            all: unset;
            border: 1px solid var(--color-4);
            border-radius: 4px;
            background-color: var(--color-5);
            min-width: 20rem;
            padding: 1rem 1.5rem;
        }
}
</style>
