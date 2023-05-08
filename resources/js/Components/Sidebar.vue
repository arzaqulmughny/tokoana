<script setup>
import { ref } from "vue"
import SidebarLink from "./SidebarLink.vue";

const productRoutes = ref([
    {
        name: 'List',
        href: '/products/list'
    },
    {
        name: 'Categories',
        href: '/products/list'
    },
    {
        name: 'Units',
        href: '/products/list'
    },
])

const transactionRoutes = ref([
    {
        name: 'Sales',
        href: '/products/list'
    },
    {
        name: 'Stock in',
        href: '/products/list'
    },
    {
        name: 'Stock out',
        href: '/products/list'
    },
])

</script>
<template>
    <nav class="sidebar" ref="sidebar">
        <div class="brand">
            <img
                src="assets/images/logo.svg"
                alt="brand icon"
                class="brand__icon"
            />
            <span class="brand__name">TOKOANA</span>
        </div>
￼
￼


        <div class="sidebar__links">
            <SidebarLink
                :class="{'link--active' : $page.url === '/'}"
                :name="'Dashboard'"
                :href="'/'"
                :icon="'iconoir-view-grid'"
                :type="'normal'"
            />

            <SidebarLink
                :class="{'link--active' : $page.url === '/suppliers'}"
                :name="'Suppliers'"
                :href="'/'"
                :icon="'iconoir-truck'"
                :type="'normal'"
            />

            <SidebarLink
                :name="'Products'"
                :href="'/'"
                :icon="'iconoir-box-iso'"
                :type="'accordion'"
                :routes="productRoutes"
            />

            <SidebarLink
                :name="'Transactions'"
                :href="'/'"
                :icon="'iconoir-data-transfer-both'"
                :type="'accordion'"
                :routes="transactionRoutes"
            />

            <SidebarLink
                :class="{'link--active' : $page.url === '/history'}"
                :name="'History'"
                :href="'/'"
                :icon="'iconoir-clock-rotate-right'"
                :type="'normal'"
            />

            <SidebarLink
                :class="{'link--active' : $page.url === '/employee'}"
                :name="'Employee'"
                :href="'/'"
                :icon="'iconoir-group'"
                :type="'normal'"
            />
        </div>
    </nav>
</template>

<style lang="scss" scoped>
.sidebar {
    width: 20rem;
    display: none;
    flex-direction: column;
    border-right: 1px solid var(--color-4);
    min-height: 100vh;

    &--active {
        display: block;
    }
}

.brand {
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
    row-gap: 1rem;
    align-items: center;
    height: 14rem;
    justify-content: center;
    z-index: 999;
    background-color: white;

    &__icon {
        width: 4rem;
    }

    &__name {
        font-weight: 700;
        font-size: 1.2rem;
    }
}

</style>
