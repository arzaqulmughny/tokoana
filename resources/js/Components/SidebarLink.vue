<script setup>
import { Link } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";

const props = defineProps({
    name: String,
    icon: String,
    href: String,
    type: String,
    routes: Array,
    class: String,
    url: String
});
const active = ref(false);

onMounted(() => {
    window.location.pathname.startsWith(props.url) ? (active.value = true) : null;
});
</script>

<template>
    <Link class="link" :class="props.class" :href="props.href" as="a" v-if="props.type === 'normal'">
        <div class="link__label">
            <i :class="props.icon"></i>
            <span class="link__name">{{ props.name }}</span>
        </div>
    </Link>

    <button class="link" @click="active = !active" v-if="props.type === 'accordion'">
        <div class="link__label">
            <i :class="props.icon"></i>
            <span class="link__name">{{ props.name }}</span>
            <svg
                class="link__arrow"
                :class="{
                    'link__arrow--active': active
                }"
                width="24px"
                height="24px"
                stroke-width="1.5"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                color="#000000"
                data-darkreader-inline-color=""
                style="--darkreader-inline-color: #e8e6e3"
            >
                <path
                    d="M6 9l6 6 6-6"
                    stroke="#000000"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    data-darkreader-inline-stroke=""
                    style="--darkreader-inline-stroke: #000000"
                ></path>
            </svg>
        </div>
        <div
            class="children"
            :class="{
                'children--active': active
            }"
        >
            <Link
                class="link"
                :class="{ 'link--active': $page.url.startsWith(route.href) }"
                :href="route.href"
                as="a"
                v-for="route in props.routes"
                @click="active = !active"
            >
                <div class="link__label">
                    <div class="link__icon"></div>
                    <span class="link__name">{{ route.name }}</span>
                </div>
            </Link>
        </div>
    </button>
</template>

<style lang="scss" scoped>
.link {
    all: unset;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    min-width: 100%;
    position: relative;

    &__label {
        display: flex;
        align-items: center;
        column-gap: 2rem;
        padding: 1rem 2rem;

        &:hover {
            background-color: var(--color-5-hover);
        }
    }

    &__icon {
        width: 1rem;
        color: var(--color-1);
    }

    &__name {
        color: var(--color-1);
    }

    &--active {
        background-color: var(--color-2-light);
        position: relative;

        .link {
            border: 1px solid red;
        }

        &:hover {
            background-color: var(--color-2-light-hover);
        }

        &::before {
            content: "";
            position: absolute;
            left: 0;
            width: 0.7rem;
            max-width: 100%;
            height: 100%;
            background-color: var(--color-2);
        }
    }

    &__arrow {
        margin-left: auto;
        width: 1.3rem;
        position: absolute;
        right: 0;
        margin-right: 2rem;
        transform: rotate(0deg);
        transition: 0.3s;

        path {
            stroke: var(--color-1);
        }

        &--active {
            transform: rotate(180deg);
            transition: 0.3s;
        }
    }
}

.children {
    background-color: var(--color-5);
    max-height: 0px;
    overflow-y: hidden;
    transition: max-height 0.1s;

    &--active {
        transition: max-height 0.3s;
        max-height: 500px;
    }
}
</style>
