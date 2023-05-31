<script setup>
import { ref } from "vue";
import SidebarLink from "./SidebarLink.vue";
import Button from "./Button.vue";

const productRoutes = ref([
    {
        name: "List",
        href: "/product/list",
    },
    {
        name: "Categories",
        href: "/product/categories",
    },
    {
        name: "Units",
        href: "/product/units",
    },
]);

const transactionRoutes = ref([
    {
        name: "Sales",
        href: "/transaction/sales",
    },
    {
        name: "Stock in",
        href: "/transaction/in",
    },
    {
        name: "Stock out",
        href: "/transaction/out",
    },
]);

const toggleSidebar = () => {
    document.querySelector(".sidebar").classList.toggle("sidebar--active");
};
</script>
<template>
    <nav class="sidebar" ref="sidebar">
        <Button
            :icon="'iconoir-cancel'"
            :variant="'clear'"
            :class="'sidebar__close'"
            @click="toggleSidebar"
        />
        <div class="brand">
            <img
                src="/assets/images/logo.svg"
                alt="brand icon"
                class="brand__icon"
            />
            <span class="brand__name">TOKOANA</span>
        </div>

        <div class="sidebar__links">
            <SidebarLink
                :class="{ 'link--active': $page.url === '/' }"
                :name="'Dashboard'"
                :href="'/'"
                :icon="'iconoir-view-grid'"
                :type="'normal'"
            />

            <SidebarLink
                :class="{ 'link--active': $page.url.startsWith('/suppliers') }"
                :name="'Suppliers'"
                :href="'/suppliers'"
                :icon="'iconoir-truck'"
                :type="'normal'"
            />

            <SidebarLink
                :name="'Products'"
                :icon="'iconoir-box-iso'"
                :type="'accordion'"
                :routes="productRoutes"
                :url="'/product'"
            />

            <SidebarLink
                :name="'Transactions'"
                :href="'/'"
                :icon="'iconoir-data-transfer-both'"
                :type="'accordion'"
                :routes="transactionRoutes"
                :url="'/transaction'"
            />

            <SidebarLink
                :class="{ 'link--active': $page.url === '/history' }"
                :name="'History'"
                :href="'/'"
                :icon="'iconoir-clock-rotate-right'"
                :type="'normal'"
            />

            <SidebarLink
                :class="{ 'link--active': $page.url === '/employee' }"
                :name="'Employee'"
                :href="'/'"
                :icon="'iconoir-group'"
                :type="'normal'"
            />
        </div>
    </nav>
</template>

<style lang="scss" scoped>
@use "./../../css/app.scss";

.sidebar {
    width: 100%;
    flex-direction: column;
    border-right: 1px solid var(--color-4);
    height: 100%;
    position: absolute;
    z-index: 999;
    top: 0;
    left: -9999px;
    overflow-y: scroll;
    background-color: var(--color-5);
    transition: all 0.3s ease-in-out;

    @include app.screen(md) {
        width: 20rem;
        position: sticky;
        margin-left: 0px;
    }

    &::-webkit-scrollbar {
        display: none;
    }

    &__close {
        position: absolute;
        right: 0.5rem;
        top: 0.5rem;
        z-index: 999999;

        @include app.screen(md) {
            display: none;
        }
    }

    &--active {
        left: 0px;
        transition: all 0.3s ease-in-out;

        @include app.screen(md) {
            margin-left: -9999px;
        }
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
