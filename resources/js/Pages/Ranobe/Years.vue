<script setup>
import { defineOptions } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import '../../../css/pages/ranobe/years.css';
import '../../../css/components/dropdown.css';
import Breadcrumbs from '../../Components/Breadcrumbs.vue';
import Collapse from '../../Components/Collapse.vue';

const props = defineProps({
    years_list: {
        type: Array,
        default: () => [],
    },
});

defineOptions({
    layout: AppLayout,
});
</script>

<template>
    <Breadcrumbs :items="[{ text: 'ГЛАВНАЯ', href: route('home') }, { text: 'РАНОБЭ' }]" />

    <svg style="display: none">
        <symbol id="check-circle" viewBox="0 0 25 25">
            <path
                d="M18.7498 1.68062C24.7247 5.12975 26.7747 12.7778 23.3248 18.7513C19.8748 24.7248 12.2249 26.7743 6.24993 23.3252C2.51247 21.1632 0.149998 17.2267 0 12.9028V12.103C0.224998 5.20474 5.99994 -0.206399 12.8999 0.00604749C14.9498 0.0685317 16.9623 0.643387 18.7498 1.66813V1.68062ZM17.1373 9.11625C16.6873 8.66636 15.9873 8.62887 15.4873 9.01627L15.3748 9.11625L11.2624 13.2277L9.6499 11.6156L9.5374 11.5156C8.98741 11.0907 8.21241 11.1907 7.78742 11.7406C7.43742 12.1905 7.43742 12.8153 7.78742 13.2777L7.88742 13.3902L10.3874 15.8895L10.4999 15.9895C10.9499 16.3394 11.5874 16.3394 12.0374 15.9895L12.1499 15.8895L17.1498 10.8908L17.2498 10.7783C17.6373 10.2785 17.5873 9.57863 17.1498 9.12875L17.1373 9.11625Z"
                fill="#56CC64" />
        </symbol>
        <symbol id="clock-logo" viewBox="0 0 25 25">
            <path
                d="M18.7498 1.68062C24.7247 5.12975 26.7747 12.7778 23.3248 18.7513C19.8748 24.7248 12.2249 26.7743 6.24993 23.3252C2.38748 21.0882 0 16.9643 0 12.5029V12.103C0.224998 5.20474 5.99994 -0.206399 12.8999 0.00604749C14.9498 0.0685317 16.9623 0.643387 18.7498 1.66813M12.4999 5.00479C11.8124 5.00479 11.2499 5.56714 11.2499 6.25447V12.6654L11.2874 12.8028L11.3374 12.9653L11.3999 13.0902L11.4624 13.1902L11.5124 13.2652L11.5999 13.3652L11.7124 13.4652L11.8124 13.5401L15.5623 16.0395C16.1373 16.4269 16.9123 16.2644 17.2998 15.6896C17.6873 15.1147 17.5248 14.3399 16.9498 13.9525L13.7499 11.8281V6.25447C13.7499 5.61713 13.2749 5.09226 12.6499 5.01728H12.4999V5.00479Z"
                fill="#FFB147" />
        </symbol>
    </svg>

    <div class="year-grid">
        <div v-for="year in years_list" :key="year.year_number" class="year-card">
            <img
                :src="`/images/ranobe/years/y${year.year_number}v1.webp`"
                class="year-card__cover"
                :alt="`Обложка ${year.year_readable}`" />
            <div class="year-card__body">
                <h1 class="year-card__title">{{ year.year_readable }}</h1>
                <Collapse class="info-block">
                    <template #trigger>
                        ПОДРОБНАЯ ИНФОРМАЦИЯ
                        <svg class="dropdown-icon"><use href="#dropdown" /></svg>
                    </template>
                    <dl class="info-block__grid">
                        <dt class="info-block__label">Всего томов:</dt>
                        <dd class="info-block__value">{{ year.volumes_count }}</dd>
                        <dt class="info-block__label">Всего глав:</dt>
                        <dd class="info-block__value">{{ year.chapters_count }}</dd>
                        <dt class="info-block__label">Всего страниц:</dt>
                        <dd class="info-block__value">{{ year.total_pages ?? 0 }}</dd>
                        <dt class="info-block__label">Всего слов:</dt>
                        <dd class="info-block__value">{{ year.words_quantity }}</dd>
                        <dt class="info-block__label">Время чтения:</dt>
                        <dd class="info-block__value">{{ year.hours_of_reading }}</dd>
                    </dl>
                </Collapse>
                <Link class="link-pill year-card__link" :href="route('ranobe.year', { year: year.year_number })"
                    >ПЕРЕЙТИ</Link
                >
                <div class="year-card__status">
                    <svg class="year-card__status-icon">
                        <use :href="year.status === 'Завершён' ? '#check-circle' : '#clock-logo'" />
                    </svg>
                    <p
                        class="year-card__status-text"
                        :class="
                            year.status === 'Завершён'
                                ? 'year-card__status-text--completed'
                                : 'year-card__status-text--ongoing'
                        ">
                        {{ year.status }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
