<script setup>
    import Sidebar from '../Components/Sidebar.vue';
    import Topbar from '../Components/Topbar.vue'
</script>
<template>
  <div class="layout">
        <div class="layout__left">
            <Sidebar />
        </div>

        <div class="layout__right">
            <Topbar />
            <slot />
        </div>
    </div>
</template>

<style lang="scss" scoped>
    .layout {
        width: 100%;
        display: flex;

        &__right {
            width: 100%;
        }
    }
</style>
