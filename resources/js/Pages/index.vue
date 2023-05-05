<script setup>
defineProps(["name"]);
</script>

<template>
    <div class="container">
        <h1 class="container__title">Hello {{ name }}</h1>
    </div>
</template>

<style lang="scss" scoped>
.container {
    &__title {
        color: blue;
    }
}
</style>
