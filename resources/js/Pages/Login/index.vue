<script setup>
import { ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import Input from "../../Components/Input.vue";
import Button from "../../Components/Button.vue";

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
    <Head>
        <title>Login</title>
    </Head>
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
                    <Input
                        :name="username"
                        :displayName="'Username'"
                        :icon="'iconoir-user'"
                        :type="'text'"
                        :placeholder="'Username'"
                        :value="loginFormData.username"
                        @update="
                            (newValue) => (loginFormData.username = newValue)
                        "
                        :error="errors.username"
                    />

                    <Input
                        :name="password"
                        :displayName="'Password'"
                        :icon="'iconoir-lock'"
                        :type="'password'"
                        :placeholder="'Password'"
                        :value="loginFormData.password"
                        @update="
                            (newValue) => (loginFormData.password = newValue)
                        "
                        :error="errors.password"
                    />

                    <small
                        class="form__invalid"
                        v-if="$page.props.flash.login_invalid"
                        >{{ $page.props.flash.login_invalid }}</small
                    >
                    <Button
                        :text="'Login'"
                        :type="'submit'"
                        :variant="'primary'"
                    />
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped lang="scss">
@use "./../../../css/app";
.login {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;

    &__left {
        max-height: 100vh;
        overflow: hidden;
        display: none;

        @include app.screen(lg) {
            display: inherit;
        }
    }

    &__image {
        pointer-events: none;
        object-fit: cover;
    }

    &__right {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        padding: 2rem;

        @include app.screen(lg) {
            min-width: 40rem;
            width: 40%;
            padding: 4rem;
        }
    }
}
.form {
    width: 100%;
    max-width: 35rem;
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

    &__subtitle {
        color: var(--color-3);
    }

    &__main {
        display: flex;
        flex-direction: column;
        row-gap: 3rem;

        > *:last-child {
            margin-left: auto;
            padding-inline: 3rem;
        }
    }

    &__invalid {
        color: red;
    }
}
</style>
