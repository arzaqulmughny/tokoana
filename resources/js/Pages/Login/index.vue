<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const loginFormData = ref({
    username: "",
    password: "",
});

defineProps({ errors: Object });

const authenticate = () => {
    router.post("/login", loginFormData.value);
};
</script>

<template>
    <div class="login">
        <div class="login__left">
            <img class="login__image" src="assets/images/login.jpg" alt="" />
        </div>
        <div class="login__right">
            <form class="form" @submit.prevent="authenticate">
                <div class="form__header">
                    <h1 class="form__title">Login</h1>
                    <h2 class="form__subtitle">Please login to continue</h2>
                </div>
                <div class="form__main">
                    <div class="form__items">
                        <label class="form__label" for="username"
                            >Username</label
                        >
                        <div class="input">
                            <svg
                                class="input__icon"
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
                                    d="M5 20v-1a7 7 0 017-7v0a7 7 0 017 7v1M12 12a4 4 0 100-8 4 4 0 000 8z"
                                    stroke="#000000"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    data-darkreader-inline-stroke=""
                                    style="--darkreader-inline-stroke: #000000"
                                ></path>
                            </svg>
                            <input
                                class="input__text"
                                type="text"
                                name="username"
                                id="username"
                                placeholder="Username"
                                autofocus
                                v-model="loginFormData.username"
                            />
                        </div>
                        <small class="input__invalid" v-if="errors.username">{{
                            errors.username
                        }}</small>
                    </div>

                    <div class="form__items">
                        <label class="form__label" for="password"
                            >Password</label
                        >
                        <div class="input">
                            <svg
                                class="inputpassword__icon"
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
                                    d="M16 12h1.4a.6.6 0 01.6.6v6.8a.6.6 0 01-.6.6H6.6a.6.6 0 01-.6-.6v-6.8a.6.6 0 01.6-.6H8m8 0V8c0-1.333-.8-4-4-4S8 6.667 8 8v4m8 0H8"
                                    stroke="#000000"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    data-darkreader-inline-stroke=""
                                    style="--darkreader-inline-stroke: #000000"
                                ></path>
                            </svg>
                            <input
                                class="input__text"
                                type="password"
                                name="password"
                                id="password"
                                placeholder="Password"
                                v-model="loginFormData.password"
                            />
                        </div>
                        <small class="input__invalid" v-if="errors.password">{{
                            errors.password
                        }}</small>
                    </div>

                    <small
                        class="input__invalid"
                        v-if="$page.props.flash.login_invalid"
                        >{{ $page.props.flash.login_invalid }}</small
                    >
                    <button class="button button--blue" type="submit">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped lang="scss">
.login {
    display: flex;

    &__left {
        max-height: 100vh;
        overflow: hidden;
    }

    &__right {
        width: 40%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}
.form {
    padding: 3rem;
    width: 100%;
    display: flex;
    flex-direction: column;
    row-gap: 3rem;

    &__header {
        display: flex;
        flex-direction: column;
        row-gap: 1rem;
    }

    &__title {
        font-size: 2.5rem;
        font-weight: 500;
        color: var(--color-1);
    }

    &__items {
        display: flex;
        flex-direction: column;
        row-gap: 1rem;
    }

    &__label {
        color: var(--color-1);
    }

    &__subtitle {
        color: var(--color-3);
    }

    &__main {
        display: flex;
        flex-direction: column;
        row-gap: 3rem;

        > *:last-child {
            margin-left: auto;
        }
    }
}

.input {
    border-radius: 4px;
    border: 1px solid var(--color-4);
    display: flex;
    padding: 10px 25px;
    column-gap: 25px;

    &__icon {
        min-width: 24px;

        path {
            stroke: var(--color-1);
        }
    }

    &__text {
        all: unset;
        width: 100%;

        &::placeholder {
            color: var(--color-3);
        }
    }

    &__invalid {
        color: red;
    }
}

.button {
    all: unset;
    padding: 1rem 3rem;
    border-radius: 4px;
    border: 1px solid var(--color-4);
    width: fit-content;
    cursor: pointer;

    &--blue {
        background-color: var(--color-2);
        color: var(--color-5);
    }
}
</style>
