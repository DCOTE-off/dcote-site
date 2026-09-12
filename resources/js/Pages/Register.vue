<script setup>
import { defineOptions, nextTick, ref } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { useTurnstile } from '../Composables/useTurnstile';
import '../../css/pages/login-reg.css';
import Breadcrumbs from '../Components/Breadcrumbs.vue';

const props = defineProps({
    siteKey: String,
});

defineOptions({
    layout: AppLayout,
});

const form = useForm({
    tag: '',
    nickname: '',
    password: '',
    password_confirmation: '',
    'cf-turnstile-response': null,
});

const fields = ['tag', 'nickname', 'password', 'password_confirmation'];

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

function togglePassword() {
    showPassword.value = !showPassword.value;
}

function togglePasswordConfirmation() {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
}

const { container: captchaContainer, ready: captchaReady } = useTurnstile({
    siteKey: props.siteKey,
    onSuccess: (token) => {
        form['cf-turnstile-response'] = token;
    },
});

function focusFirstError() {
    const firstKey = fields.find((field) => form.errors[field]);
    if (firstKey) {
        nextTick(() => document.getElementById(firstKey)?.focus());
    }
}

function clearError(field) {
    form.clearErrors(field);
}

function submit() {
    form.post(route('register'), {
        onError: focusFirstError,
    });
}
</script>

<template>

    <Breadcrumbs :items="[
        { text: 'ГЛАВНАЯ', href: route('home') },
        { text: 'АВТОРИЗАЦИЯ', href: route('login') },
        { text: 'РЕГИСТРАЦИЯ' },
    ]" />
    <div class="auth">
        <div class="auth__image">
            <picture>
                <source media="(max-width: 768px)" :srcset="'/images/auth/auth_4-mobile.webp'" type="image/webp">
                <img :src="'/images/auth/auth_4.webp'" alt="Изображение в регистрации">
            </picture>
        </div>
        <div class="auth__panel">
            <form class="auth__form" id="registration-form" novalidate @submit.prevent="submit">
                <h1 class="auth__title">РЕГИСТРАЦИЯ</h1>
                <div class="auth__field">
                    <label for="tag">
                        <h3>Имя пользователя</h3>
                    </label>
                    <input
                        id="tag"
                        class="auth__input"
                        v-model="form.tag"
                        placeholder="Логин для возможности входа в аккаунт"
                        autocomplete="username"
                        spellcheck="false"
                        pattern="^[a-z0-9_]{5,32}$"
                        title="От 5 до 32 символов: только маленькая латиница, цифры и '_'"
                        required
                        @input="clearError('tag')">
                    <span class="auth__error" v-if="form.errors.tag">{{ form.errors.tag }}</span>
                </div>
                <div class="auth__field">
                    <label for="nickname">
                        <h3>Отображаемое имя</h3>
                    </label>
                    <input
                        id="nickname"
                        class="auth__input"
                        v-model="form.nickname"
                        placeholder="Никнейм, отображаемый в профиле"
                        spellcheck="false"
                        pattern=".{1,64}$"
                        title="Не менее 1 и не более 64 символов"
                        required
                        @input="clearError('nickname')">
                    <span class="auth__error" v-if="form.errors.nickname">{{ form.errors.nickname }}</span>
                </div>
                <div class="auth__field">
                    <div class="auth__password-title">
                        <label for="password">
                            <h3>Пароль</h3>
                        </label>
                    </div>
                    <div class="auth__password">
                        <input
                            class="auth__input"
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            v-model="form.password"
                            placeholder="Не менее 8 символов"
                            autocomplete="new-password"
                            pattern=".{8,72}$"
                            title="Не менее 8 и не более 72 символов"
                            required
                            @input="clearError('password')">
                        <button
                            type="button"
                            class="auth__password-toggle"
                            :aria-label="showPassword ? 'Скрыть пароль' : 'Показать пароль'"
                            :aria-pressed="showPassword"
                            @click="togglePassword">
                            <img v-if="!showPassword" class="auth__password-icon" :src="'/svgs/eye.svg'" alt="" aria-hidden="true">
                            <img v-else class="auth__password-icon" :src="'/svgs/disabled-eye.svg'" alt="" aria-hidden="true">
                        </button>
                    </div>
                    <span class="auth__error" v-if="form.errors.password">{{ form.errors.password }}</span>
                </div>
                <div class="auth__field">
                    <div class="auth__password-title">
                        <label for="password_confirmation">
                            <h3>Повторный ввод пароля</h3>
                        </label>
                    </div>
                    <div class="auth__password">
                        <input
                            class="auth__input"
                            :type="showPasswordConfirmation ? 'text' : 'password'"
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            placeholder="Повторите пароль с поля выше"
                            autocomplete="new-password"
                            required
                            @input="clearError('password_confirmation')">
                        <button
                            type="button"
                            class="auth__password-toggle"
                            :aria-label="showPasswordConfirmation ? 'Скрыть пароль' : 'Показать пароль'"
                            :aria-pressed="showPasswordConfirmation"
                            @click="togglePasswordConfirmation">
                            <img v-if="!showPasswordConfirmation" class="auth__password-icon" :src="'/svgs/eye.svg'" alt="" aria-hidden="true">
                            <img v-else class="auth__password-icon" :src="'/svgs/disabled-eye.svg'" alt="" aria-hidden="true">
                        </button>
                    </div>
                    <span class="auth__error" v-if="form.errors.password_confirmation">{{ form.errors.password_confirmation }}</span>
                </div>
                <p class="auth__check">Регистрируясь, вы принимаете <Link :href="route('rules')">правила сайта</Link> и <Link :href="route('privacy_policy')">политику конфиденциальности</Link></p>
                <div class="auth__captcha">
                    <div ref="captchaContainer"></div>
                </div>
                <div class="auth__field">
                    <button type="submit" class="auth__submit btn-pill" :disabled="!captchaReady || form.processing">ЗАРЕГИСТРИРОВАТЬСЯ</button>
                </div>
            </form>
        </div>
    </div>
</template>