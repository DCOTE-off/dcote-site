<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import Breadcrumbs from '../../Components/Breadcrumbs.vue';
import RatingWidget from '../../Components/RatingWidget.vue';
import '../../../css/pages/anime/episode.css';
import '../../../css/components/player-status.css';

const props = defineProps({
    season: {
        type: Number,
        required: true,
    },
    episode: {
        type: Number,
        required: true,
    },
    episodeId: {
        type: Number,
        required: true,
    },
    episodeUrl: {
        type: String,
        default: '',
    },
    totalEpisodes: {
        type: Number,
        default: 0,
    },
    completed: {
        type: Boolean,
        default: false,
    },
    prevLink: {
        type: String,
        default: null,
    },
    nextLink: {
        type: String,
        default: null,
    },
    episodeAvgRating: {
        type: [Number, String],
        default: 0,
    },
    episodeRatingsCount: {
        type: Number,
        default: 0,
    },
    episodeUserRating: {
        type: Number,
        default: 0,
    },
});

defineOptions({
    layout: AppLayout,
});

const PLAYER_LOAD_TIMEOUT_MS = 12000;

const playerRef = ref(null);
const playerError = ref(false);
let loadTimer = null;

function clearLoadTimer() {
    if (loadTimer) {
        window.clearTimeout(loadTimer);
        loadTimer = null;
    }
}

function hidePlayerError() {
    clearLoadTimer();
    playerError.value = false;
}

function watchPlayerLoad() {
    hidePlayerError();
    loadTimer = window.setTimeout(() => {
        playerError.value = true;
    }, PLAYER_LOAD_TIMEOUT_MS);
}

function retryPlayer() {
    watchPlayerLoad();

    const player = playerRef.value;
    if (!player) {
        return;
    }

    const currentSource = player.src;
    player.src = 'about:blank';
    window.setTimeout(() => {
        player.src = currentSource;
    }, 0);
}

onMounted(() => {
    if (props.completed) {
        watchPlayerLoad();
    }
});

onBeforeUnmount(clearLoadTimer);
</script>

<template>
    <Breadcrumbs :items="[
        { text: 'ГЛАВНАЯ', href: route('home') },
        { text: 'АНИМЕ', href: route('anime.index') },
        { text: `${season} СЕЗОН`, href: route('anime.season', { season }) },
        { text: `${episode} СЕРИЯ` },
    ]" />

    <div v-if="completed" class="episode-rating">
        <RatingWidget
            rateable-type="anime_episode"
            :rateable-id="episodeId"
            :user-rating="episodeUserRating"
            :avg-rating="Number(episodeAvgRating) || 0"
            :ratings-count="episodeRatingsCount" />
    </div>

    <h1 v-if="!completed" class="episode-empty">СЕРИИ ПОКА НЕТ</h1>

    <div v-else class="episode-player-shell">
        <iframe
            id="episode-iframe-player"
            ref="playerRef"
            v-show="!playerError"
            :src="episodeUrl"
            allow="autoplay; encrypted-media; picture-in-picture"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            loading="lazy"
            @load="hidePlayerError">
        </iframe>
        <div class="episode-player-status" role="status" v-show="playerError">
            <h2>ПЛЕЕР ВРЕМЕННО НЕДОСТУПЕН</h2>
            <p>Проверьте соединение и попробуйте загрузить его ещё раз.</p>
            <button class="btn-pill" type="button" @click="retryPlayer">
                ПОВТОРИТЬ
            </button>
        </div>
    </div>

    <div class="episode-controls">
        <component
            :is="prevLink ? Link : 'a'"
            class="link-pill episode-nav-button prev-episode-btn"
            :class="{ disabled: !prevLink }"
            :href="prevLink || undefined">
            <svg class="episode-arrow-icon" aria-hidden="true">
                <use href="#arrow-left" />
            </svg>
            <span>ПРЕДЫДУЩАЯ СЕРИЯ</span>
        </component>
        <Link class="link-pill no-glow all-episodes-btn" :href="route('anime.season', { season })">
            <span>ВСЕ СЕРИИ</span>
            <svg class="episode-arrow-icon episode-arrow-icon-down" aria-hidden="true">
                <use href="#arrow-down" />
            </svg>
        </Link>
        <component
            :is="nextLink ? Link : 'a'"
            class="link-pill episode-nav-button next-episode-btn"
            :class="{ disabled: !nextLink }"
            :href="nextLink || undefined">
            <span>СЛЕДУЮЩАЯ СЕРИЯ</span>
            <svg class="episode-arrow-icon" aria-hidden="true">
                <use href="#arrow-right" />
            </svg>
        </component>
    </div>

    <div class="episode-controls mobile">
        <component
            :is="prevLink ? Link : 'a'"
            class="link-pill episode-nav-button prev-episode-btn"
            :class="{ disabled: !prevLink }"
            :href="prevLink || undefined">
            <svg class="episode-arrow-icon" aria-hidden="true">
                <use href="#arrow-left" />
            </svg>
            <span>НАЗАД</span>
        </component>
        <Link class="link-pill no-glow all-episodes-btn" :href="route('anime.season', { season })">
            <span>СЕРИИ</span>
            <svg class="episode-arrow-icon episode-arrow-icon-down" aria-hidden="true">
                <use href="#arrow-down" />
            </svg>
        </Link>
        <component
            :is="nextLink ? Link : 'a'"
            class="link-pill episode-nav-button next-episode-btn"
            :class="{ disabled: !nextLink }"
            :href="nextLink || undefined">
            <span>ВПЕРЁД</span>
            <svg class="episode-arrow-icon" aria-hidden="true">
                <use href="#arrow-right" />
            </svg>
        </component>
    </div>
</template>
