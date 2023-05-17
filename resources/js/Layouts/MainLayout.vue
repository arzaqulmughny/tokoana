<script setup>
import Sidebar from "../Components/Sidebar.vue";
import Topbar from "../Components/Topbar.vue";
</script>
<template>
    <div class="layout">
        <div class="layout__left">
            <Sidebar />
        </div>

        <div class="layout__right" scroll-region>
            <Topbar />
            <main class="main">
                <slot />
            </main>
        </div>
    </div>
</template>

<style lang="scss">
.layout {
    width: 100%;
    display: flex;
    height: 100vh;
    max-height: 100vh;
    position: fixed;
    max-width: 1440px;

    &__right {
        width: 100%;
        overflow: auto;
    }
}

.main {
    background-color: var(--color-bg);
    padding: 2rem;
    padding-bottom: 15rem;
    width: 100%;
    display: flex;
    flex-direction: column;
    row-gap: 1.5rem;
    min-height: 100%;

    &__title {
        font-size: 1.5rem;
        font-weight: 500;
        color: var(--color-1);
    }
}
</style>
