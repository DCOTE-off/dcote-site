<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import SiteFooter from '../Components/SiteFooter.vue';
import SiteHeader from '../Components/SiteHeader.vue';
import ToastContainer from '../Components/ToastContainer.vue';
import { pushToast } from '../stores/toast';

const page = usePage();
const openMenu = ref(null);

function toggleMenu(menu) {
    openMenu.value = openMenu.value === menu ? null : menu;
}

function onClickOutside(event) {
    if (!openMenu.value) return;

    if (event.target.closest('.account-menu, .side-menu, .account-menu__trigger, .mobile-nav__item')) {
        return;
    }

    openMenu.value = null;
}

// Меню живут в персистентном лейауте, поэтому после SPA-перехода их нужно
// закрывать вручную — иначе открытая менюшка «переезжает» на новую страницу.
let stopNavigationListener = null;

onMounted(() => {
    document.addEventListener('click', onClickOutside);
    stopNavigationListener = router.on('navigate', () => {
        openMenu.value = null;
    });
});

onBeforeUnmount(() => {
    document.removeEventListener('click', onClickOutside);
    stopNavigationListener?.();
});

watch(
    () => [page.props.flash?.success, page.props.flash?.error],
    ([success, error]) => {
        if (success) {
            pushToast('success', success);
        } else if (error) {
            pushToast('error', error);
        }
    },
    { immediate: true },
);
</script>

<template>
    <SiteHeader
        :open-menu="openMenu"
        @toggle-menu="toggleMenu"
    />
    <main>
        <slot />
    </main>
    <SiteFooter
        @toggle-menu="toggleMenu"
    />

    <ToastContainer />
</template>
