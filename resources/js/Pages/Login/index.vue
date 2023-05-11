<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import Input from "../../Components/Input.vue";

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
