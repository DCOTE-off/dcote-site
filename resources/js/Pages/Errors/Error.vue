<script setup>
import { computed, defineOptions } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import '../../../css/pages/page-404.css';

const props = defineProps({
    status: {
        type: Number,
        default: 500,
    },
});

defineOptions({
    layout: AppLayout,
});

const ERRORS = {
    403: { image: '/images/errors/ptichka-500.webp', text: 'ДОСТУП ЗАПРЕЩЁН' },
    404: {
        image: '/images/errors/ptichka-404.webp',
        text: 'СОМНЕВАЮСЬ, ЧТО ТАКАЯ СТРАНИЦА СУЩЕСТВУЕТ',
    },
    405: { image: '/images/errors/ptichka-500.webp', text: 'ТАК ДЕЛАТЬ НЕЛЬЗЯ' },
    429: { image: '/images/errors/ptichka-500.webp', text: 'СЛИШКОМ МНОГО ЗАПРОСОВ' },
};

const content = computed(
    () =>
        ERRORS[props.status] ?? {
            image: '/images/errors/ptichka-500.webp',
            text: 'ПРОБЛЕМАТИЧНО, ЧТО-ТО ПОШЛО НЕ ТАК',
        },
);
</script>

<template>
    <div class="wrapper">
        <h1 class="text-404">{{ status }}</h1>
        <img class="ptichka" :src="content.image" alt="Иллюстрация ошибки" />
        <div class="desc-404">
            <h2>{{ content.text }}</h2>
            <div class="button">
                <Link class="return btn-pill" :href="route('home')">
                    <h3>НА ГЛАВНУЮ</h3>
                </Link>
            </div>
        </div>
    </div>
</template>
