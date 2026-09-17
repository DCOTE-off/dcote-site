<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import RatingWidget from './RatingWidget.vue';

const props = defineProps({
    season: {
        type: Number,
        required: true,
    },
    serverNow: {
        type: String,
        default: '',
    },
    episode: {
        type: Object,
        required: true,
    },
});

const BOOKMARK_PREFIX = 'dcote:anime:bookmark-state:v1';
const WATCH_PREFIX = 'dcote:anime:watch-state:v1';

function readStoredState(storageKey, activeState, fallbackState) {
    try {
        return localStorage.getItem(storageKey) === activeState ? activeState : fallbackState;
    } catch {
        return fallbackState;
    }
}

function persistState(storageKey, state) {
    try {
        localStorage.setItem(storageKey, state);
    } catch {
        // localStorage недоступен — игнорируем
    }
}

const bookmarkKey = `${BOOKMARK_PREFIX}:${props.season}:${props.episode.episode_number}`;
const watchKey = `${WATCH_PREFIX}:${props.season}:${props.episode.episode_number}`;

const bookmarked = ref(readStoredState(bookmarkKey, 'bookmarked', 'unbookmarked'));
const watched = ref(readStoredState(watchKey, 'watched', 'unwatched'));

const coverRef = ref(null);

function pulse(className) {
    const el = coverRef.value;
    if (!el) {
        return;
    }

    el.classList.add(className);
    window.setTimeout(() => el.classList.remove(className), 300);
}

function toggleBookmark(event) {
    event.preventDefault();
    event.stopPropagation();

    if (props.episode.is_upcoming) {
        return;
    }

    const nextState = bookmarked.value === 'bookmarked' ? 'unbookmarked' : 'bookmarked';
    persistState(bookmarkKey, nextState);
    bookmarked.value = nextState;
    pulse('is-bookmark-interacting');
}

function toggleWatch(event) {
    event.preventDefault();
    event.stopPropagation();

    if (props.episode.is_upcoming) {
        return;
    }

    const nextState = watched.value === 'watched' ? 'unwatched' : 'watched';
    persistState(watchKey, nextState);
    watched.value = nextState;
    pulse('is-watch-state-interacting');
}

function openInNewTab(event) {
    event.preventDefault();
    event.stopPropagation();

    window.open(episodeUrl.value, '_blank', 'noopener,noreferrer');
}

const episodeUrl = computed(() => route('anime.episode', {
    season: props.season,
    episode: props.episode.episode_number,
}));

const countdownText = ref('Дата уточняется');
let countdownTimer;

function formatRemaining(milliseconds) {
    const totalMinutes = Math.ceil(milliseconds / 60000);
    const days = Math.floor(totalMinutes / 1440);
    const hours = Math.floor((totalMinutes % 1440) / 60);
    const minutes = totalMinutes % 60;

    const day = new Intl.NumberFormat('ru', { style: 'unit', unit: 'day', unitDisplay: 'long' });
    const hour = new Intl.NumberFormat('ru', { style: 'unit', unit: 'hour', unitDisplay: 'long' });
    const minute = new Intl.NumberFormat('ru', { style: 'unit', unit: 'minute', unitDisplay: 'long' });

    return [
        days > 0 ? day.format(days) : null,
        hours > 0 ? hour.format(hours) : null,
        minute.format(minutes),
    ].filter(Boolean).join(' ');
}

function setupCountdown() {
    if (!props.episode.is_upcoming || !props.episode.appear_at) {
        return;
    }

    const releaseAt = Date.parse(props.episode.appear_at);
    if (Number.isNaN(releaseAt)) {
        return;
    }

    const serverNow = Date.parse(props.serverNow);
    const serverOffset = Number.isNaN(serverNow) ? 0 : serverNow - Date.now();

    function tick() {
        const now = Date.now() + serverOffset;
        const remaining = releaseAt - now;

        if (remaining <= 0) {
            window.location.reload();
            return;
        }

        countdownText.value = formatRemaining(remaining);
        countdownTimer = window.setTimeout(tick, Math.max(1000, Math.min(60000, remaining)));
    }

    tick();
}

setupCountdown();

onBeforeUnmount(() => {
    window.clearTimeout(countdownTimer);
});
</script>

<template>
    <div
        class="episode"
        :class="{ 'has-appear-in': episode.is_upcoming }"
        :data-episode-number="episode.episode_number"
        :data-is-upcoming="episode.is_upcoming ? 'true' : 'false'"
        :data-bookmark-state="bookmarked"
        :data-watch-state="watched"
        :data-appear-at="episode.appear_at || undefined">
        <a class="episode__link" :href="episodeUrl">
            <div class="episode__title episode__title--mobile">
                <h3 class="episode__number">
                    <span>{{ episode.episode_number }} серия</span>
                    <img
                        class="episode__open-new-tab"
                        :src="'/svgs/open-in-new-tab.svg'"
                        alt="Открыть серию в новой вкладке"
                        role="link"
                        tabindex="0"
                        @click.prevent.stop="openInNewTab"
                        @keydown.enter.prevent="openInNewTab">
                </h3>
                <p class="episode__heading">{{ episode.episode_name }}</p>
            </div>
            <div ref="coverRef" class="episode__cover" :class="{ 'has-appear-in': episode.is_upcoming }">
                <img class="episode__cover-image" :src="`/images/anime/episodes-banner-season${season}.webp`">
                <span
                    class="episode__bookmark episode__bookmark--mobile"
                    :class="{ 'is-disabled': episode.is_upcoming }"
                    role="button"
                    tabindex="0"
                    :aria-label="bookmarked === 'bookmarked' ? 'Удалить серию из закладок' : 'Добавить серию в закладки'"
                    :aria-pressed="bookmarked === 'bookmarked'"
                    :title="bookmarked === 'bookmarked' ? 'Удалить из закладок' : 'Добавить в закладки'"
                    @click.prevent.stop="toggleBookmark"
                    @keydown.enter.prevent="toggleBookmark">
                    <span class="episode__bookmark-icon" aria-hidden="true"></span>
                </span>
                <span
                    class="episode__watch"
                    :class="{
                        'is-disabled': episode.is_upcoming,
                        'is-watched': watched === 'watched',
                        'is-unwatched': watched !== 'watched',
                    }"
                    role="button"
                    tabindex="0"
                    :aria-label="watched === 'watched' ? 'Отметить серию непросмотренной' : 'Отметить серию просмотренной'"
                    :aria-pressed="watched === 'watched'"
                    :title="watched === 'watched' ? 'Просмотрено' : 'Не просмотрено'"
                    @click.prevent.stop="toggleWatch"
                    @keydown.enter.prevent="toggleWatch">
                    <img
                        :src="watched === 'watched' ? '/svgs/eye.svg' : '/svgs/disabled-eye.svg'"
                        alt=""
                        aria-hidden="true">
                </span>
                <div v-if="episode.is_upcoming" class="episode__appear episode__appear--mobile">
                    <p>До выхода серии:</p>
                    <h3>{{ countdownText }}</h3>
                </div>
            </div>
            <div class="episode__title">
                <h3 class="episode__number">
                    <span>{{ episode.episode_number }} серия</span>
                    <img
                        class="episode__open-new-tab"
                        :src="'/svgs/open-in-new-tab.svg'"
                        alt="Открыть серию в новой вкладке"
                        role="link"
                        tabindex="0"
                        @click.prevent.stop="openInNewTab"
                        @keydown.enter.prevent="openInNewTab">
                </h3>
                <p class="episode__heading">{{ episode.episode_name }}</p>
                <div class="episode__meta">
                    <span
                        class="episode__bookmark episode__bookmark--desktop"
                        :class="{ 'is-disabled': episode.is_upcoming }"
                        role="button"
                        tabindex="0"
                        :aria-label="bookmarked === 'bookmarked' ? 'Удалить серию из закладок' : 'Добавить серию в закладки'"
                        :aria-pressed="bookmarked === 'bookmarked'"
                        :title="bookmarked === 'bookmarked' ? 'Удалить из закладок' : 'Добавить в закладки'"
                        @click.prevent.stop="toggleBookmark"
                        @keydown.enter.prevent="toggleBookmark">
                        <span class="episode__bookmark-icon" aria-hidden="true"></span>
                    </span>
                    <span
                        class="episode__comments episode__comments--desktop"
                        :class="{ 'is-disabled': episode.is_upcoming }"
                        :aria-label="`Комментариев: ${episode.comments_count}`"
                        :title="`Комментариев: ${episode.comments_count}`">
                        <img :src="'/svgs/message1.svg'" alt="" aria-hidden="true">
                        <span class="episode__comments-count">{{ episode.comments_count }}</span>
                    </span>
                </div>
            </div>
        </a>
        <a v-if="episode.is_upcoming" class="episode__appear episode__appear--desktop" :href="episodeUrl">
            <div class="episode__appear--desktop-wrapper">
                <p>До выхода серии:</p>
                <h3>{{ countdownText }}</h3>
            </div>
        </a>
        <div class="episode__footer">
            <span
                class="episode__comments episode__comments--mobile"
                :class="{ 'is-disabled': episode.is_upcoming }"
                :aria-label="`Комментариев: ${episode.comments_count}`"
                :title="`Комментариев: ${episode.comments_count}`">
                <img :src="'/svgs/message1.svg'" alt="" aria-hidden="true">
                <span class="episode__comments-count">{{ episode.comments_count }}</span>
            </span>
            <a :href="episode.trailer_link" class="link-pill-outline episode__trailer--mobile" style="border-color: rgba(98, 59, 146, 1);">ТРЕЙЛЕР</a>
            <div class="episode__rating">
                <RatingWidget
                    rateable-type="anime_episode"
                    :rateable-id="episode.id"
                    :user-rating="episode.user_rating"
                    :avg-rating="episode.avg_rating"
                    :ratings-count="episode.ratings_count"
                    :disabled="episode.is_upcoming"
                    no-extra />
            </div>
            <a :href="episode.trailer_link" class="link-pill-outline episode__trailer--desktop" style="border-color: rgba(98, 59, 146, 1);">ТРЕЙЛЕР СЕРИИ</a>
        </div>
    </div>
</template>