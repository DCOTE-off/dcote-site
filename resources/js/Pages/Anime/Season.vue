<script setup>
import { computed, defineOptions, nextTick, ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { usePrefersReducedMotion, useIsMobile } from '../../Composables/useMediaQuery.js';
import '../../../css/pages/anime/season.css';
import '../../../css/components/list-filter.css';
import '../../../css/components/dropdown.css';
import Breadcrumbs from '../../Components/Breadcrumbs.vue';
import EpisodeCard from '../../Components/EpisodeCard.vue';
import ClampedText from '../../Components/ClampedText.vue';
import Comments from '../../Pages/Comments.vue';

const props = defineProps({
    season: {
        type: Number,
        required: true,
    },
    about_season: {
        type: Object,
        default: () => ({}),
    },
    showReleaseSchedule: {
        type: Boolean,
        default: false,
    },
    server_now: {
        type: String,
        default: '',
    },
    episodes: {
        type: Array,
        default: () => [],
    },
    season_id: {
        type: Number,
        required: true,
    },
});

defineOptions({
    layout: AppLayout,
});

const prefersReducedMotion = usePrefersReducedMotion();
const isMobile = useIsMobile();

const seasonDescription = computed(() => {
    const value = (props.about_season.season_description ?? '').trim();

    return !value || value.toLowerCase() === 'null' ? 'Описание сезона' : value;
});

const sortCriterion = ref('episode');
const sortDirection = ref('descending');
const expanded = ref(false);
const displayExpanded = ref(false);
const filterOpen = ref(false);
const gridAreaRef = ref(null);
const isToggling = ref(false);
const isSorting = ref(false);

const upcomingCount = computed(() => props.episodes.filter((episode) => episode.is_upcoming).length);
const releasedLimit = computed(() => (upcomingCount.value ? 3 : 4));

function getEpisodeRating(episode) {
    return Number(episode.avg_rating ?? 0) || 0;
}

function getOrderedEpisodes(direction, criterion) {
    const directionMultiplier = direction === 'descending' ? -1 : 1;
    const upcoming = props.episodes
        .filter((episode) => episode.is_upcoming)
        .sort((a, b) => a.episode_number - b.episode_number);
    const released = props.episodes
        .filter((episode) => !episode.is_upcoming)
        .sort((a, b) => {
            const primaryA = criterion === 'rating' ? getEpisodeRating(a) : a.episode_number;
            const primaryB = criterion === 'rating' ? getEpisodeRating(b) : b.episode_number;
            const primaryDifference = (primaryA - primaryB) * directionMultiplier;

            if (primaryDifference !== 0) {
                return primaryDifference;
            }

            return (a.episode_number - b.episode_number) * directionMultiplier;
        });

    return [...upcoming, ...released];
}

const orderedEpisodes = computed(() => getOrderedEpisodes(sortDirection.value, sortCriterion.value));

function getCollapsedNumbers(episodes) {
    const numbers = new Set();
    const upcoming = episodes.filter((episode) => episode.is_upcoming);
    const released = episodes.filter((episode) => !episode.is_upcoming);

    upcoming.slice(0, 1).forEach((episode) => numbers.add(episode.episode_number));
    released.slice(0, releasedLimit.value).forEach((episode) => numbers.add(episode.episode_number));

    return numbers;
}

const collapsedVisibleNumbers = ref(getCollapsedNumbers(orderedEpisodes.value));

function isEpisodeVisible(episode) {
    return displayExpanded.value || collapsedVisibleNumbers.value.has(episode.episode_number);
}

const canCollapse = computed(() =>
    orderedEpisodes.value.some((episode) => !collapsedVisibleNumbers.value.has(episode.episode_number)),
);

function isGroupSeparator(episode) {
    if (episode.is_upcoming) {
        return false;
    }

    const hasUpcoming = orderedEpisodes.value.some((item) => item.is_upcoming && isEpisodeVisible(item));
    const firstReleased = orderedEpisodes.value.find((item) => !item.is_upcoming && isEpisodeVisible(item));

    return hasUpcoming && firstReleased?.id === episode.id;
}

async function toggleExpanded() {
    if (isToggling.value) {
        return;
    }

    const listEl = gridAreaRef.value;
    if (!listEl) {
        return;
    }

    const nextExpanded = !expanded.value;
    const shouldAnimate =
        !prefersReducedMotion.value &&
        typeof listEl.animate === 'function' &&
        typeof listEl.firstElementChild?.animate === 'function';

    expanded.value = nextExpanded;

    if (!shouldAnimate) {
        displayExpanded.value = nextExpanded;
        return;
    }

    isToggling.value = true;
    listEl.classList.add('is-list-toggling');

    const collapsibleNumbers = collapsedVisibleNumbers.value;
    const startHeight = listEl.getBoundingClientRect().height;
    let endHeight;

    if (nextExpanded) {
        displayExpanded.value = true;
        await nextTick();
        endHeight = listEl.getBoundingClientRect().height;
    } else {
        const items = Array.from(listEl.children);
        const collapsibleItems = items.filter((el) => !collapsibleNumbers.has(Number(el.dataset.episodeNumber)));

        collapsibleItems.forEach((el) => el.classList.add('is-collapsed-hidden'));
        endHeight = listEl.getBoundingClientRect().height;
        collapsibleItems.forEach((el) => el.classList.remove('is-collapsed-hidden'));
    }

    const items = Array.from(listEl.children);
    const collapsibleItems = items.filter((el) => !collapsibleNumbers.has(Number(el.dataset.episodeNumber)));

    const containerAnimation = listEl.animate([{ height: `${startHeight}px` }, { height: `${endHeight}px` }], {
        duration: 520,
        easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
        fill: 'both',
    });

    const itemAnimations = collapsibleItems.map((el, index) =>
        el.animate(
            nextExpanded
                ? [
                      { opacity: 0, transform: 'translateY(calc(-1 * var(--fs-gap20)))' },
                      { opacity: 1, transform: 'translateY(0)' },
                  ]
                : [
                      { opacity: 1, transform: 'translateY(0)' },
                      { opacity: 0, transform: 'translateY(calc(-1 * var(--fs-gap20)))' },
                  ],
            {
                duration: 360,
                delay: nextExpanded ? Math.min(index * 25, 150) : 0,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                fill: 'both',
            },
        ),
    );

    await Promise.all([
        containerAnimation.finished.catch(() => {}),
        ...itemAnimations.map((animation) => animation.finished.catch(() => {})),
    ]);

    containerAnimation.cancel();
    itemAnimations.forEach((animation) => animation.cancel());

    if (!nextExpanded) {
        displayExpanded.value = false;
    }

    listEl.classList.remove('is-list-toggling');
    isToggling.value = false;
}

function animateSortedItem(item, firstRect, animateStableItems) {
    const lastRect = item.getBoundingClientRect();
    const offsetX = (firstRect?.left ?? lastRect.left) - lastRect.left;
    const offsetY = (firstRect?.top ?? lastRect.top) - lastRect.top;
    const positionChanged = Math.abs(offsetX) >= 0.5 || Math.abs(offsetY) >= 0.5;

    if (!positionChanged && !animateStableItems) {
        return Promise.resolve();
    }

    if (!positionChanged) {
        item.style.willChange = 'transform, opacity';

        return item
            .animate(
                [
                    { opacity: 0.72, transform: 'translate3d(0, var(--fs-gap10), 0)' },
                    { opacity: 1, transform: 'translate3d(0, 0, 0)' },
                ],
                {
                    duration: 360,
                    easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                },
            )
            .finished.finally(() => {
                item.style.willChange = '';
            });
    }

    item.style.willChange = 'transform';

    return item
        .animate([{ transform: `translate3d(${offsetX}px, ${offsetY}px, 0)` }, { transform: 'translate3d(0, 0, 0)' }], {
            duration: 520,
            easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
        })
        .finished.finally(() => {
            item.style.willChange = '';
        });
}

let pendingSort = null;

async function applySort(newDirection, newCriterion, animateStableItems = false) {
    const listEl = gridAreaRef.value;
    if (!listEl) {
        return;
    }

    if (isSorting.value) {
        pendingSort = { newDirection, newCriterion, animateStableItems };
        return;
    }

    const nextCollapsedNumbers = getCollapsedNumbers(getOrderedEpisodes(newDirection, newCriterion));
    collapsedVisibleNumbers.value = nextCollapsedNumbers;

    if (!displayExpanded.value) {
        Array.from(listEl.children).forEach((el) => {
            const shouldHide = !nextCollapsedNumbers.has(Number(el.dataset.episodeNumber));
            el.classList.toggle('is-collapsed-hidden', shouldHide);
        });
    }

    const visibleItems = Array.from(listEl.children).filter((el) => !el.classList.contains('is-collapsed-hidden'));
    const shouldAnimate =
        !prefersReducedMotion.value && visibleItems.length > 0 && typeof visibleItems[0].animate === 'function';
    const firstRects = shouldAnimate ? new Map(visibleItems.map((el) => [el, el.getBoundingClientRect()])) : null;

    sortDirection.value = newDirection;
    sortCriterion.value = newCriterion;

    if (!shouldAnimate) {
        return;
    }

    isSorting.value = true;
    listEl.classList.add('is-sorting');
    await nextTick();

    const newVisibleItems = Array.from(listEl.children).filter((el) => !el.classList.contains('is-collapsed-hidden'));
    const animations = newVisibleItems.map((item) => animateSortedItem(item, firstRects.get(item), animateStableItems));

    await Promise.all(animations.map((animation) => animation.catch(() => {})));

    listEl.classList.remove('is-sorting');
    isSorting.value = false;

    if (pendingSort) {
        const pending = pendingSort;
        pendingSort = null;
        applySort(pending.newDirection, pending.newCriterion, pending.animateStableItems);
    }
}

function toggleSort() {
    const nextDirection = sortDirection.value === 'descending' ? 'ascending' : 'descending';
    applySort(nextDirection, sortCriterion.value);
}

function selectCriterion(criterion) {
    if (criterion !== sortCriterion.value) {
        applySort(sortDirection.value, criterion, true);
    }

    filterOpen.value = false;
}
</script>

<template>
    <Breadcrumbs
        :items="[
            { text: 'ГЛАВНАЯ', href: route('home') },
            { text: 'АНИМЕ', href: route('anime.index') },
            { text: `${season} СЕЗОН` },
        ]" />

    <div class="season-card">
        <div class="season-card__cover">
            <picture>
                <source
                    media="(max-width: 768px)"
                    :srcset="`/images/anime/anime-banner-season-${season}-mobile.webp`"
                    type="image/webp" />
                <img :src="`/images/anime/anime-banner-season-${season}.webp`" decoding="async" alt="Обложка сезона" />
            </picture>
        </div>
        <div class="season-card__desc">
            <h1 class="season-card__title">{{ season }} СЕЗОН АНИМЕ-АДАПТАЦИИ</h1>
            <ClampedText
                class="season-card__description"
                :text="seasonDescription"
                :lines="6"
                :always-expanded="!isMobile">
                <template #button="{ toggle, expanded, id }">
                    <button
                        type="button"
                        class="season-card__read-more"
                        :aria-controls="id"
                        :aria-expanded="expanded"
                        @click="toggle">
                        {{ expanded ? 'меньше' : 'больше' }}
                    </button>
                </template>
            </ClampedText>
            <div class="season-card__actions">
                <button
                    class="btn-pill-outline"
                    style="border-color: rgba(98, 59, 146, 1); gap: var(--fs-gap10)"
                    disabled
                    aria-expanded="false">
                    ДОБАВИТЬ В
                    <svg class="dropdown-icon"><use href="#dropdown" /></svg>
                </button>
                <Link class="link-pill" :href="route('anime.episode', { season, episode: 1 })">НАЧАТЬ СМОТРЕТЬ</Link>
                <a
                    v-if="about_season.trailer_link"
                    :href="about_season.trailer_link"
                    class="link-pill-outline"
                    style="border-color: rgba(98, 59, 146, 1)"
                    >ТРЕЙЛЕР СЕЗОНА</a
                >
            </div>
            <div class="season-card__actions--mobile">
                <Link class="link-pill" :href="route('anime.episode', { season, episode: 1 })">НАЧАТЬ СМОТРЕТЬ</Link>
                <div class="season-card__actions-group">
                    <button
                        class="btn-pill-outline"
                        style="border-color: rgba(98, 59, 146, 1); gap: var(--fs-gap10)"
                        disabled
                        aria-expanded="false">
                        ДОБАВИТЬ В
                        <svg class="dropdown-icon"><use href="#dropdown" /></svg>
                    </button>
                    <a
                        v-if="about_season.trailer_link"
                        :href="about_season.trailer_link"
                        class="link-pill-outline"
                        style="border-color: rgba(98, 59, 146, 1)"
                        >ТРЕЙЛЕР</a
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="episodes">
        <div class="episodes__head">
            <button
                type="button"
                class="episodes__sort episodes__control btn-pill-outline"
                style="border-color: rgba(98, 59, 146, 1)"
                aria-label="Сортировать по возрастанию/убыванию"
                @click="toggleSort">
                <svg v-show="sortDirection === 'descending'" class="episodes__sort-icon" aria-hidden="true">
                    <use href="#sort-descending-filled-compact" />
                </svg>
                <svg v-show="sortDirection === 'ascending'" class="episodes__sort-icon" aria-hidden="true">
                    <use href="#sort-ascending-filled-compact" />
                </svg>
                <span>СОРТИРОВКА</span>
            </button>
            <h1 class="episodes__title">СПИСОК СЕРИЙ</h1>
            <div class="list-filter episodes__filter" data-list-filter="episodes" :class="{ 'is-open': filterOpen }">
                <button
                    type="button"
                    class="filter-toggle episodes__control btn-pill-outline"
                    style="border-color: rgba(98, 59, 146, 1)"
                    :aria-expanded="filterOpen"
                    aria-haspopup="listbox"
                    aria-controls="episode-filter-menu"
                    @click="filterOpen = !filterOpen">
                    <svg class="episodes__filter-icon" aria-hidden="true">
                        <use href="#filter-filled" />
                    </svg>
                    <span>ФИЛЬТР</span>
                    <svg class="dropdown-icon" aria-hidden="true">
                        <use href="#dropdown" />
                    </svg>
                </button>
                <div
                    id="episode-filter-menu"
                    class="list-filter-menu"
                    role="listbox"
                    aria-label="Критерий сортировки серий">
                    <button
                        type="button"
                        class="list-filter-option"
                        :class="{ 'is-selected': sortCriterion === 'episode' }"
                        role="option"
                        :aria-selected="sortCriterion === 'episode'"
                        data-sort-criterion="episode"
                        @click="selectCriterion('episode')">
                        <svg class="list-filter-option-icon list-filter-option-icon--episodes" aria-hidden="true">
                            <use href="#side-menu-anime" />
                        </svg>
                        <span>По сериям</span>
                    </button>
                    <button
                        type="button"
                        class="list-filter-option"
                        :class="{ 'is-selected': sortCriterion === 'rating' }"
                        role="option"
                        :aria-selected="sortCriterion === 'rating'"
                        data-sort-criterion="rating"
                        @click="selectCriterion('rating')">
                        <svg class="list-filter-option-icon list-filter-option-icon--rating" aria-hidden="true">
                            <use href="#star" />
                        </svg>
                        <span>По оценкам</span>
                    </button>
                </div>
            </div>
        </div>
        <p v-if="showReleaseSchedule" class="episodes__schedule">
            Каждая новая серия выходит в <b>среду</b> в <b>15:30 по МСК</b>! Русские субтитры появляются на сайте спустя
            <b>полчаса-час</b>.
        </p>
        <div
            id="episodes-list"
            ref="gridAreaRef"
            class="episodes__list"
            :data-season="season"
            :data-server-now="server_now">
            <EpisodeCard
                v-for="episode in orderedEpisodes"
                :key="episode.id"
                :season="season"
                :server-now="server_now"
                :episode="episode"
                :class="{
                    'is-collapsed-hidden': !isEpisodeVisible(episode),
                    'has-group-separator': isGroupSeparator(episode),
                }"
                :aria-hidden="!isEpisodeVisible(episode) ? 'true' : undefined" />
        </div>
        <button
            v-if="canCollapse"
            type="button"
            class="episodes__toggle dropdown-btn"
            aria-controls="episodes-list"
            :class="{ 'is-expanded': expanded }"
            :aria-expanded="expanded"
            @click="toggleExpanded">
            <span>{{ expanded ? 'Свернуть' : 'Развернуть' }}</span>
            <svg class="dropdown-icon" aria-hidden="true">
                <use href="#dropdown" />
            </svg>
        </button>
    </div>
    <Comments :comment-label="`К ${season} СЕЗОНУ АНИМЕ`" commentable-type="anime_season" :commentable-id="season_id" />
</template>
