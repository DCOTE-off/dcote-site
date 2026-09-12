<script setup>
import { defineOptions } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useIsMobile } from '../../Composables/useMediaQuery.js';
import '../../../css/components/media-card.css';
import '../../../css/components/dropdown.css';
import Breadcrumbs from '../../Components/Breadcrumbs.vue';
import Collapse from '../../Components/Collapse.vue';
import RatingWidget from '../../Components/RatingWidget.vue';

const props = defineProps({
    year: {
        type: Number,
        required: true,
    },
    volumes: {
        type: Array,
        default: () => [],
    },
});

defineOptions({
    layout: AppLayout,
});

const isMobile = useIsMobile();

function volumePercent(volume) {
    const total = volume.all_chapters;
    return total > 0 ? Math.min(100, Math.round((volume.chapters_count / total) * 100)) : 0;
}
</script>

<template>

    <Breadcrumbs :items="[
        { text: 'ГЛАВНАЯ', href: route('home') },
        { text: 'РАНОБЭ', href: route('ranobe.index') },
        { text: `${year} ГОД` },
    ]" />

    <div
        v-for="(volume, index) in volumes"
        :key="volume.id"
        class="media-card"
        :data-volume="Math.trunc(volume.volume_number)">
        <div class="media-card__cover">
            <picture>
                <source media="(max-width: 768px)" :srcset="volume.cover_image_mobile" type="image/webp">
                <img
                    :src="volume.cover_image"
                    :fetchpriority="index < 2 ? 'high' : undefined"
                    :loading="index < 2 ? undefined : 'lazy'"
                    decoding="async"
                    :alt="`Обложка ${volume.volume_number} тома ${year} года`">
            </picture>
        </div>
        <div class="media-card__desc">
            <div class="media-card__head">
                <h1>{{ volume.volume_number }} ТОМ</h1>
                <RatingWidget
                    rateable-type="ranobe_volume"
                    :rateable-id="volume.id"
                    :user-rating="volume.volume_user_rating"
                    :avg-rating="volume.volume_avg_rating"
                    :ratings-count="volume.volume_ratings_count" />
            </div>
            <Collapse :always-open="!isMobile" class="info-panel">
                <template #trigger>
                    <div class="info-panel__title">ОСНОВНАЯ ИНФОРМАЦИЯ <svg v-if="isMobile" class="dropdown-icon"><use href="#dropdown" /></svg></div>
                </template>
                <dl class="info-panel__grid">
                    <dt class="info-panel__label">Статус издания:</dt>
                    <dd class="info-panel__value" :class="volume.color">{{ volume.status }}</dd>
                    <dt class="info-panel__label">Общая нумерация:</dt>
                    <dd class="info-panel__value">{{ volume.general_number }}</dd>
                    <dt class="info-panel__label">Дата выхода (книга):</dt>
                    <dd class="info-panel__value">{{ volume.release_date_book }}</dd>
                    <dt class="info-panel__label">Дата выхода (цифра):</dt>
                    <dd class="info-panel__value">{{ volume.release_date_digital }}</dd>
                    <dt class="info-panel__label">Объём тома:</dt>
                    <dd class="info-panel__value">{{ volume.all_chapters }}</dd>
                    <dt class="info-panel__label">ISBN книги:</dt>
                    <dd class="info-panel__value">{{ volume.isbn }}</dd>
                </dl>
            </Collapse>
            <div class="media-card__progress">
                <p><b>Переведено:</b> {{ volume.chapters_count }} из {{ volume.all_chapters }} глав</p>
                <div class="media-card__progress-bar" :style="{ '--progress-width': `${volumePercent(volume)}%` }"></div>
            </div>
            <Link class="link-pill" :href="route('ranobe.volume', { year: year, volume: volume.volume_number })">СТРАНИЦА ТОМА</Link>
            <div class="media-card__buttons">
                <button class="link-pill-outline" style="border-color: rgba(98, 59, 146, 1);" disabled aria-expanded="false">ДОБАВИТЬ В
                    <svg class="dropdown-icon"><use href="#dropdown" /></svg>
                </button>
                <Link class="link-pill-outline" style="border-color: rgba(98, 59, 146, 1);" :href="route('ranobe.chapter', { year: year, volume: volume.volume_number, chapter: 1 })">НАЧАТЬ ЧИТАТЬ</Link>
            </div>
            <div class="media-card__buttons--mobile">
                <button class="btn-pill-outline" style="border-color: rgba(98, 59, 146, 1);" disabled aria-expanded="false">ДОБАВИТЬ В
                    <svg class="dropdown-icon"><use href="#dropdown" /></svg>
                </button>
            </div>
            <div class="media-card__buttons--mobile">
                <Link class="link-pill-outline" style="border-color: rgba(98, 59, 146, 1);" :href="route('ranobe.chapter', { year: year, volume: volume.volume_number, chapter: 1 })">НАЧАТЬ ЧИТАТЬ</Link>
            </div>
        </div>
    </div>
</template>