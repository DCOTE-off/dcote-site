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
    password: '',
    remember: false,
    'cf-turnstile-response': null,
});

const fields = ['tag', 'password'];

const showPassword = ref(false);

function togglePassword() {
    showPassword.value = !showPassword.value;
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
    form.post(route('login'), {
        onError: focusFirstError,
    });
}
</script>

<template>
    <Breadcrumbs :items="[{ text: 'ГЛАВНАЯ', href: route('home') }, { text: 'АВТОРИЗАЦИЯ' }]" />
    <div class="auth">
        <div class="auth__image">
            <picture>
                <source media="(max-width: 768px)" :srcset="'/images/auth/auth_4-mobile.webp'" type="image/webp" />
                <img :src="'/images/auth/auth_4.webp'" alt="Изображение в регистрации" />
            </picture>
        </div>
        <div class="auth__panel">
            <form id="registration-form" class="auth__form" novalidate @submit.prevent="submit">
                <h1 class="auth__title">АВТОРИЗАЦИЯ</h1>
                <div class="auth__field-tip">
                    <div class="auth__field">
                        <label for="tag">
                            <h3>Имя пользователя</h3>
                        </label>
                        <input
                            id="tag"
                            v-model="form.tag"
                            class="auth__input"
                            spellcheck="false"
                            name="tag"
                            required
                            autocomplete="username"
                            @input="clearError('tag')" />
                        <span v-if="form.errors.tag" class="auth__error">{{ form.errors.tag }}</span>
                    </div>
                    <span class="auth__tip">Забыл(а) имя пользователя? <a href="">Восстановить</a></span>
                </div>
                <div class="auth__field-tip">
                    <div class="auth__field">
                        <div class="auth__password-title">
                            <label for="password">
                                <h3>Пароль</h3>
                            </label>
                        </div>
                        <div class="auth__password">
                            <input
                                id="password"
                                v-model="form.password"
                                class="auth__input"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                autocomplete="password"
                                title="Не менее 8 и не более 72 символов"
                                required
                                @input="clearError('password')" />
                            <button
                                type="button"
                                class="auth__password-toggle"
                                :aria-label="showPassword ? 'Скрыть пароль' : 'Показать пароль'"
                                :aria-pressed="showPassword"
                                @click="togglePassword">
                                <img
                                    v-if="!showPassword"
                                    class="auth__password-icon"
                                    :src="'/svgs/eye.svg'"
                                    alt=""
                                    aria-hidden="true" />
                                <img
                                    v-else
                                    class="auth__password-icon"
                                    :src="'/svgs/disabled-eye.svg'"
                                    alt=""
                                    aria-hidden="true" />
                            </button>
                        </div>
                        <span v-if="form.errors.password" class="auth__error">{{ form.errors.password }}</span>
                    </div>
                    <span class="auth__tip">Забыл(а) пароль? <a href="">Восстановить</a></span>
                </div>
                <div class="auth__captcha">
                    <div ref="captchaContainer"></div>
                </div>
                <div class="auth__field">
                    <button type="submit" class="auth__submit btn-pill" :disabled="!captchaReady || form.processing">
                        АВТОРИЗОВАТЬСЯ
                    </button>
                    <span class="auth__tip"
                        >Нет аккаунта? <Link :href="route('register')"> Зарегистрироваться</Link></span
                    >
                </div>
            </form>
        </div>
    </div>
</template>
