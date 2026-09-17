<script setup>
import { defineOptions } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';
import Breadcrumbs from '../Components/Breadcrumbs.vue';

defineOptions({
    layout: AppLayout,
});

defineProps({
    title: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Breadcrumbs :items="[
        { text: 'ГЛАВНАЯ', href: route('home') },
        { text: title },
    ]" />
    <section class="coming-soon">
        <h1 class="coming-soon__title">{{ title }}</h1>
        <p class="coming-soon__text">Раздел в разработке.</p>
    </section>
</template>

<style scoped>
.coming-soon {
    align-items: center;
    display: flex;
    flex-direction: column;
    gap: var(--fs-gap20);
    padding: calc(var(--fs-gap20) * 4) var(--fs-gap20);
    text-align: center;
}

.coming-soon__title {
    font-family: 'Vag Rounded Next';
    font-weight: 700;
}
</style>
