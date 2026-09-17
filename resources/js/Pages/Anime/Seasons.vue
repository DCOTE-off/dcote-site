<script setup>
import { defineOptions } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import '../../../css/components/media-card.css';
import { useIsMobile} from '../../Composables/useMediaQuery.js';
import '../../../css/components/dropdown.css';
import '../../../css/components/rating.css';
import Breadcrumbs from '../../Components/Breadcrumbs.vue';
import Collapse from '../../Components/Collapse.vue';

const props = defineProps({
    seasons_list: {
        type: Array,
        default: () => [],
    },
});

defineOptions({
    layout: AppLayout,
});

const isMobile = useIsMobile();

function seasonPercent(season) {
    const total = season.number_of_episodes;
    return total > 0 ? Math.min(100, Math.round((season.released_episodes_count / total) * 100)) : 0;
}

function avgRating(season) {
    return Number(season.season_avg_rating ?? 0).toFixed(1);
}
</script>

<template>

    <Breadcrumbs :items="[
        { text: 'ГЛАВНАЯ', href: route('home') },
        { text: 'АНИМЕ' },
    ]" />

    <div
        v-for="(season, index) in seasons_list"
        :key="season.season_number"
        class="media-card"
        :data-season="season.season_number">
        <div class="media-card__cover">
            <picture>
                <source media="(max-width: 768px)" :srcset="`/images/anime/anime-banner-season-${season.season_number}-mobile.webp`" type="image/webp">
                <img
                    :src="season.img_src || `/images/anime/anime-banner-season-${season.season_number}.webp`"
                    :fetchpriority="index < 2 ? 'high' : undefined"
                    :loading="index < 2 ? undefined : 'lazy'"
                    decoding="async"
                    alt="Обложка сезона">
            </picture>
        </div>
        <div class="media-card__desc">
            <div class="media-card__head">
                <h1>{{ season.season_number }} СЕЗОН</h1>
                <div
                    class="rating-widget"
                    data-rateable-type="anime_episode"
                    data-rateable-id="0"
                    data-user-rating="0"
                    :data-avg-rating="season.season_avg_rating"
                    :data-ratings-count="season.season_ratings_count">
                    <div class="star-and-avg">
                        <svg class="star-rating-icon visual" viewBox="0 0 36 35">
                            <use href="#star" />
                        </svg>
                        <h3 class="rating-value">{{ avgRating(season) }}</h3>
                    </div>
                    <div class="extra-info">
                        <p>Всего оценок: <span class="ratings-count">{{ season.season_ratings_count }}</span></p>
                    </div>
                </div>
            </div>
            <Collapse :always-open="!isMobile" class="info-panel">
                <template #trigger>
                    <div class="info-panel__title">ОСНОВНАЯ ИНФОРМАЦИЯ <svg v-if="isMobile" class="dropdown-icon"><use href="#dropdown" /></svg></div>
                </template>
                <dl class="info-panel__grid">
                    <dt class="info-panel__label">Статус сериала:</dt>
                    <dd class="info-panel__value" :class="season.color">{{ season.status}}</dd>
                    <dt class="info-panel__label">Сезон выпуска:</dt>
                    <dd class="info-panel__value">{{ season.season_time==='null' ? 'Не определено' : season.season_time}}</dd>
                    <dt class="info-panel__label">День релиза:</dt>
                    <dd class="info-panel__value">{{ season.release_time==='null' ? 'Не определено' : season.release_time}}</dd>
                    <dt class="info-panel__label">Студия:</dt>
                    <dd class="info-panel__value">{{ season.studio==='null' ? 'Не определено' : season.studio }}</dd>
                    <dt class="info-panel__label">Кол-во серий:</dt>
                    <dd class="info-panel__value">{{ season.number_of_episodes===0 ? 'Не определено' : season.number_of_episodes}}</dd>
                    <dt class="info-panel__label">{{ isMobile ? 'Экранизация:' : 'Экранизируемые тома:' }}</dt>
                    <dd class="info-panel__value">{{ season.adapt_volumes==='null' ? 'Не определено' : season.adapt_volumes}} {{ season.adapt_volumes_brackets ==='null' ? '' : season.adapt_volumes_brackets}}</dd>
                </dl>
            </Collapse>
            <div class="media-card__progress">
                <p><b>Выпущено:</b> {{ season.released_episodes_count }} из {{ season.number_of_episodes===0 ? '?' : season.number_of_episodes }} серий</p>
                <div class="media-card__progress-bar" :style="{ '--progress-width': `${seasonPercent(season)}%` }"></div>
            </div>
            <button v-if="season.is_announced" type="button" class="btn-pill" disabled>СТРАНИЦА СЕЗОНА</button>
            <Link v-else class="link-pill" :href="route('anime.season', { season: season.season_number })">СТРАНИЦА СЕЗОНА</Link>
            <div class="media-card__buttons">
                <button class="btn-pill-outline" style="border-color: rgba(98, 59, 146, 1); gap: var(--fs-gap10);" disabled aria-expanded="false">ДОБАВИТЬ В
                    <svg class="dropdown-icon"><use href="#dropdown" /></svg>
                </button>
                <button v-if="season.is_announced" type="button" class="btn-pill-outline" style="border-color: rgba(98, 59, 146, 1);" disabled>НАЧАТЬ СМОТРЕТЬ</button>
                <Link v-else class="link-pill-outline" style="border-color: rgba(98, 59, 146, 1);" :href="route('anime.episode', { season: season.season_number, episode: 1 })">НАЧАТЬ СМОТРЕТЬ</Link>
            </div>
            <div class="media-card__buttons--mobile">
                <button class="btn-pill-outline" style="border-color: rgba(98, 59, 146, 1); gap: var(--fs-gap10);" disabled aria-expanded="false">ДОБАВИТЬ В
                    <svg class="dropdown-icon"><use href="#dropdown" /></svg>
                </button>
            </div>
            <div class="media-card__buttons--mobile">
                <button v-if="season.is_announced" type="button" class="btn-pill-outline" style="border-color: rgba(98, 59, 146, 1);" disabled>НАЧАТЬ СМОТРЕТЬ</button>
                <Link v-else class="link-pill-outline" style="border-color: rgba(98, 59, 146, 1);" :href="route('anime.episode', { season: season.season_number, episode: 1 })">НАЧАТЬ СМОТРЕТЬ</Link>
            </div>
        </div>
    </div>
</template>