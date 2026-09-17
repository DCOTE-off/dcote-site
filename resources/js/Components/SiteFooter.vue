<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const emit = defineEmits(['toggle-menu']);
const page = usePage();
const user = computed(() => page.props.auth?.user);
</script>

<template>
    <footer class="site-footer">
        <div class="site-footer__left">
            <a href="https://x.com/aandreev06" class="site-footer__social-link"><svg class="site-footer__social-icon" width="50" height="50"><use href="#x-logo" /></svg></a>
            <a href="https://discord.gg/cTTwcYhbR" class="site-footer__social-link"><svg class="site-footer__social-icon" width="50" height="50"><use href="#ds-logo" /></svg></a>
            <a href="https://t.me/DCOTEFILES" class="site-footer__social-link"><svg class="site-footer__social-icon" width="50" height="50"><use href="#tg-logo" /></svg></a>
        </div>
        <div class="site-footer__center">
            <p class="site-footer__copyright">Мы не претендуем на авторство и/или какие-либо иные права на какой-либо контент с авторским правом, представленный на сайте.</p>
            <div class="site-footer__links">
            <Link :href="route('privacy_policy')" class="site-footer__link">Политика конфиденциальности</Link>
            <Link :href="route('rules')" class="site-footer__link">Правила сайта</Link>
            </div>
        </div>
        <div class="site-footer__right">
            <Link :href="route('home')">
                <svg class="main-logo"><use href="#dcote-svg" /></svg>
            </Link>
        </div>
    </footer>

    <nav class="mobile-nav">
        <Link v-if="!user" :href="route('login')" class="mobile-nav__item">
            <svg class="mobile-nav__icon" viewBox="0 0 24 24"><use href="#user" /></svg>
            <span class="mobile-nav__label">Войти</span>
        </Link>
        <button v-else class="mobile-nav__item account-menu__trigger button-without-styles-all" type="button" @click="emit('toggle-menu', 'account')">
            <svg class="mobile-nav__icon" viewBox="0 0 24 24"><use href="#user" /></svg>
            <span class="mobile-nav__label">Аккаунт</span>
        </button>
        <a :href="route('notifications')" class="mobile-nav__item">
            <svg class="mobile-nav__icon" viewBox="0 0 24 24"><use href="#mail" /></svg>
            <span class="mobile-nav__label">Оповещения</span>
        </a>
        <Link :href="route('home')" class="mobile-nav__item">
            <svg class="mobile-nav__logo"><use href="#dcote-logo-small" /></svg>
        </Link>
        <a :href="route('favorite')" class="mobile-nav__item">
            <svg class="mobile-nav__icon" viewBox="0 0 24 24"><use href="#file-star" /></svg>
            <span class="mobile-nav__label">Избранное</span>
        </a>
        <button class="mobile-nav__item button-without-styles-all" type="button" @click="emit('toggle-menu', 'side')">
            <svg class="mobile-nav__icon" viewBox="0 0 24 24"><use href="#stack-2" /></svg>
            <span class="mobile-nav__label">Меню</span>
        </button>
    </nav>
</template>