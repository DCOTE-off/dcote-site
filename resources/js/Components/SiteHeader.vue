<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    openMenu: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['toggle-menu']);
const page = usePage();

const user = computed(() => page.props.auth?.user);

function toggleMenu(menu) {
    emit('toggle-menu', menu);
}
</script>

<template>
    <nav class="site-header">
        <div class="site-header__bar">
            <div class="site-header__left">
                <Link :href="route('home')">
                    <svg class="main-logo"><use href="#dcote-svg" /></svg>
                </Link>
            </div>

            <div class="site-header__center">
                <a class="site-header__nav-link site-header__nav-link--disabled">НОВОСТИ</a>
                <Link :href="route('ranobe.index')" class="site-header__nav-link">РАНОБЭ</Link>
                <Link :href="route('anime.index')" class="site-header__nav-link">АНИМЕ</Link>
                <a class="site-header__nav-link site-header__nav-link--disabled">МАНГА</a>
                <a class="site-header__nav-link site-header__nav-link--disabled">ИЛЛЮСТРАЦИИ</a>
                <a class="site-header__nav-link site-header__nav-link--disabled">ПЕРСОНАЖИ</a>
                <Link :href="route('about-project')" class="site-header__nav-link">О ПРОЕКТЕ</Link>
            </div>

            <div class="site-header__right">
                <a href="#" class="site-header__mail" aria-label="Почта">
                    <svg class="site-header__mail-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <use href="#mail-icon" />
                    </svg>
                </a>
                <button
                    v-if="user"
                    class="site-header__account-btn account-menu__trigger btn-pill"
                    type="button"
                    @click="toggleMenu('account')">
                    АККАУНТ
                </button>
                <Link v-else :href="route('login')" class="btn-pill site-header__account-btn">ВОЙТИ</Link>
            </div>
        </div>

        <Transition name="account-menu">
            <div v-if="props.openMenu === 'account'" class="account-menu">
            <a class="link-pill side-button" :href="route('account')">
                <svg class="account-menu__icon"><use href="#user" /></svg>
                Мой профиль
            </a>
            <a class="link-pill side-button" style="background: #c6750c;" :href="route('favorite')">
                <svg class="account-menu__icon"><use href="#file-star" /></svg>
                Избранное
            </a>
            <Link class="link-pill-outline side-button" style="border-color: rgba(98, 59, 146, 1);" :href="route('rules')">
                <svg class="account-menu__icon"><use href="#info" /></svg>
                Правила сайта
            </Link>
            <a class="link-pill-outline side-button" style="border-color: rgba(98, 59, 146, 1);" :href="route('settings')">
                <svg class="account-menu__icon"><use href="#settings" /></svg>
                Настройки
            </a>
            <a v-if="page.props.auth?.can_access_admin" class="link-pill side-button" style="background:rgba(191, 63, 63, 1);" :href="route('filament.admin.pages.dashboard')">
                <svg class="account-menu__icon"><use href="#database" /></svg>
                Админ-панель
            </a>
            <form :action="route('logout')" method="POST" style="display: inline;">
                <input type="hidden" name="_token" :value="page.props.csrf_token">
                <button type="submit" class="btn-pill-outline side-button" style="border-color: rgba(98, 59, 146, 1);">
                    <svg class="account-menu__icon"><use href="#exit" /></svg>
                    Выйти с аккаунта
                </button>
            </form>
            </div>
        </Transition>

        <Transition name="side-menu">
            <div v-if="props.openMenu === 'side'" class="side-menu">
            <div class="side-menu__links">
                <Link class="link-pill side-button" :href="route('ranobe.index')">
                    <svg class="side-menu__icon"><use href="#side-menu-ranobe" /></svg>
                    РАНОБЭ
                </Link>
                <Link class="link-pill side-button" :href="route('anime.index')">
                    <svg class="side-menu__icon"><use href="#side-menu-anime" /></svg>
                    АНИМЕ
                </Link>
                <Link class="link-pill-outline side-button" :href="route('about-project')" style="border-color:rgba(98, 59, 146, 1)">
                    <svg class="side-menu__icon"><use href="#side-menu-about" /></svg>
                    О ПРОЕКТЕ
                </Link>
                <a class="link-pill-outline disabled side-button" aria-disabled="true">
                    <svg class="side-menu__icon"><use href="#side-menu-news" /></svg>
                    НОВОСТИ
                </a>
                <a class="link-pill-outline disabled side-button" aria-disabled="true">
                    <svg class="side-menu__icon"><use href="#side-menu-manga" /></svg>
                    МАНГА
                </a>
                <a class="link-pill-outline disabled side-button" aria-disabled="true">
                    <svg class="side-menu__icon"><use href="#side-menu-illustrations" /></svg>
                    ИЛЛЮСТРАЦИИ
                </a>
                <a class="link-pill-outline disabled side-button" aria-disabled="true">
                    <svg class="side-menu__icon"><use href="#side-menu-characters" /></svg>
                    ПЕРСОНАЖИ
                </a>
            </div>
            </div>
        </Transition>
    </nav>
</template>