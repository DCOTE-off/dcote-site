<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import EmblaCarousel from 'embla-carousel';
import AppLayout from '../Layouts/AppLayout.vue';
import { useIsMobile, usePrefersReducedMotion } from '../Composables/useMediaQuery';
import { COLLAPSE_ANIMATION_MS } from '../breakpoints';
import Collapse from '../Components/Collapse.vue';
import ClampedText from '../Components/ClampedText.vue';
import '../../css/pages/dcote-main.css';


const props = defineProps({
    classes_list_default: Array,
    classes_list_spoilers: Array,
    popularCards: Array,
    feed: Array,
});

defineOptions({
    layout: AppLayout,
});

function formatDate(date) {
    return new Intl.DateTimeFormat('ru-RU').format(new Date(date));
}

const isMobile = useIsMobile();
const prefersReducedMotion = usePrefersReducedMotion();

const showSpoilers = ref(false);

const heroText = 'Горячие новости из медиа-пространства произведения, чтение оригинальной новеллы, '
    + 'бесплатный просмотр аниме-адаптации и чтение глав манги. Полноценный сборник '
    + 'иллюстраций от художника Томосе Сюнсаку, арты от художников-фанатов '
    + 'и превосходные арт-генерации от ИИ. Подробные досье и описания персонажей. '
    + 'Всё это и не только вы найдёте на страницах данного веб-сообщества!';

const tgBadges = [
    {
        id: 'news',
        title: 'НОВОСТИ',
        tabTitle: 'НОВОСТИ',
        avatarChannel: '/images/index/tg-news-channel-ava.jpg',
        avatarChat: '/images/index/tg-news-chat-ava.png',
        channel: 'https://t.me/DCOTEFILES',
        chat: 'https://t.me/DCOTE2',
        text: 'Новостной канал по вселенной произведения и прикреплённый к нему чат для обсуждения горячих новостей и не только!',
    },
    {
        id: 'spoilers',
        title: 'СПОЙЛЕРЫ',
        tabTitle: 'СПОЙЛЕРЫ',
        avatarChannel: '/images/index/tg-spoilers-channel-ava.png',
        avatarChat: '/images/index/tg-spoilers-chat-ava.png',
        channel: 'https://t.me/DCOTESPOILERS2',
        chat: 'https://t.me/DCOTESPOILERSCHAT',
        text: 'Канал по самым свежим утечкам и обработки информации с прикреплённым к нему чатом для обсуждения сливов и не только!',
    },
    {
        id: 'theories',
        title: 'ТЕОРИИ И РАЗБОРЫ',
        tabTitle: 'ТЕОРИИ',
        avatarChannel: '/images/index/tg-theories-channel-ava.png',
        avatarChat: '/images/index/tg-theories-chat-ava.png',
        channel: 'https://t.me/DCOTETHEORIES',
        chat: 'https://t.me/DCOTETHEORIES2',
        text: 'Творческий канал по теориям и разбору различных моментов с чатом для обсуждения общих интересов и не только!',
    },
];

const activeTgTab = ref(tgBadges[0].id);

const gridCards = [
    {
        title: 'АНИМЕ',
        href: route('anime.index'),
        img: '/images/index/category-anime.webp',
        imgMobile: '/images/index/category-anime-mobile.webp',
        text: 'Бесплатный просмотр аниме-адаптации всех сезонов в хорошем качестве',
    },
    {
        title: 'РАНОБЭ',
        href: route('ranobe.index'),
        img: '/images/index/category-ranobe.webp',
        imgMobile: '/images/index/category-ranobe-mobile.webp',
        text: 'Чтение оригинальной новеллы в полном формате и хорошем качестве перевода',
    },
    {
        title: 'МАНГА',
        img: '/images/index/category-manga-bw.webp',
        imgMobile: '/images/index/category-manga-bw-mobile.webp',
        text: 'В разработке',
    },
    {
        title: 'ИЛЛЮСТРАЦИИ',
        img: '/images/index/category-illustrations-bw.webp',
        imgMobile: '/images/index/category-illustrations-bw-mobile.webp',
        text: 'В разработке',
    },
    {
        title: 'ПЕРСОНАЖИ',
        img: '/images/index/category-characters-bw.webp',
        imgMobile: '/images/index/category-characters-bw-mobile.webp',
        text: 'В разработке',
    },
];

const gridImagesRef = ref(null);
const activeGridIndex = ref(0);
let gridCarousel = null;
let gridCarouselSettling = false;



function initGridCarousel() {
    if (gridCarousel || !gridImagesRef.value) {
        return;
    }

    gridCarousel = EmblaCarousel(gridImagesRef.value, {
        align: 'center',
        containScroll: false,
        loop: gridCards.length > 1,
        slidesToScroll: 1,
        skipSnaps: false,
        watchDrag: (emblaApi, event) => !gridCarouselSettling,
    });

    function updateSelected() {
        activeGridIndex.value = gridCarousel.selectedScrollSnap();
    }

    gridCarousel.on('select', updateSelected);
    gridCarousel.on('reInit', updateSelected);
    gridCarousel.on('settle', () => {
        gridCarouselSettling = false;
    });

    // Обрезка невидимого хвоста: когда до цели осталось меньше X px —
    // мгновенно доезжаем (jump), чтобы анимация не «ползла» бесконечно.
    const gridTailThreshold = 0.5;
    gridCarousel.on('scroll', () => {
        const engine = gridCarousel.internalEngine();
        if (engine.dragHandler.pointerDown()) {
            return;
        }
        const remaining = Math.abs(engine.target.get() - engine.location.get());
        if (remaining < gridTailThreshold) {
            gridCarousel.scrollTo(engine.index.get(), true);
        }
    });

    updateSelected();
}

function destroyGridCarousel() {
    if (!gridCarousel) {
        return;
    }

    gridCarousel.destroy();
    gridCarousel = null;
    activeGridIndex.value = 0;
    gridCarouselSettling = false;
}

function onGridImageClick(index, event) {
    if (gridCarouselSettling) {
        event.preventDefault();
        return;
    }

    if (index === activeGridIndex.value) {
        return;
    }

    event.preventDefault();

    if (!gridCarousel) {
        return;
    }

    const root = gridImagesRef.value;
    const rootCenter = root.getBoundingClientRect().left + root.offsetWidth / 2;
    const targetCenter = event.currentTarget.getBoundingClientRect().left + event.currentTarget.offsetWidth / 2;
    const goingNext = targetCenter > rootCenter;

    if (goingNext ? !gridCarousel.canScrollNext() : !gridCarousel.canScrollPrev()) {
        return;
    }

    if (goingNext) {
        gridCarousel.scrollNext();
    } else {
        gridCarousel.scrollPrev();
    }

    gridCarouselSettling = true;
}

watch(isMobile, (mobile) => {
    if (mobile) {
        initGridCarousel();
    } else {
        destroyGridCarousel();
    }
});

const ratingVisible = ref(false);
const ratingIntroComplete = ref(false);
const ratingBlockRef = ref(null);
let ratingObserver;
const popularIndex = ref(0);
const previousPopularIndex = ref(null);
const popularDirection = ref(1);
const popularAnimating = ref(false);
let popularAnimationTimer;
const updatesAtEnd = ref(true);
const updatesNews = ref(null);
const updatesEndThreshold = 24;
const ratingCards = ref(props.classes_list_default.map((classSchool, index) => ({
    slot: `rating-slot-${index}`,
    classSchool,
})));
const ratingListRef = ref(null);

const topUsers = [
    { rank: 1, icon: 'chess-king', color: '#ffb147', rating: 700 },
    { rank: 2, icon: 'chess-queen', color: '#b2beca', rating: 600 },
    { rank: 3, icon: 'chess-queen', color: '#ce8947', rating: 500 },
    { rank: 4, icon: 'chess-rook', color: 'white', rating: 400 },
    { rank: 5, icon: 'chess-rook', color: 'white', rating: 300 },
    { rank: 6, icon: 'chess-rook', color: 'white', rating: 200 },
    { rank: 7, icon: 'chess-rook', color: 'white', rating: 100 },
];
const ratingAnimating = ref(false);
let ratingAnimationTimer;

const popularCollapseRefs = ref([]);
const popularPendingSwitch = ref(null);
let popularSwitchTimer;

function revealRating() {
    ratingVisible.value = true;

    if (prefersReducedMotion.value) {
        ratingIntroComplete.value = true;
        return;
    }

    window.setTimeout(() => {
        ratingIntroComplete.value = true;
    }, 1200);
}

function toggleSpoilers() {
    if (ratingAnimating.value) {
        return;
    }

    const targetClasses = showSpoilers.value
        ? props.classes_list_default
        : props.classes_list_spoilers;
    const currentCardsByLeader = new Map(
        ratingCards.value.map((card) => [card.classSchool.leader, card]),
    );
    const availableCards = ratingCards.value.filter((card) => !targetClasses.some(
        (classSchool) => classSchool.leader === card.classSchool.leader,
    ));

    const oldPointsWidths = new Map();
    ratingListRef.value?.$el?.querySelectorAll('.top-classes__class-points').forEach((points) => {
        const leader = points.closest('.top-classes__class')?.dataset.ratingKey;
        if (leader) {
            oldPointsWidths.set(leader, points.getBoundingClientRect().width);
        }
    });

    ratingCards.value = targetClasses.map((classSchool) => ({
        slot: currentCardsByLeader.get(classSchool.leader)?.slot ?? availableCards.shift().slot,
        classSchool,
    }));
    showSpoilers.value = !showSpoilers.value;
    ratingVisible.value = true;
    ratingIntroComplete.value = true;

    if (prefersReducedMotion.value) {
        return;
    }

    ratingAnimating.value = true;
    window.clearTimeout(ratingAnimationTimer);
    ratingAnimationTimer = window.setTimeout(() => {
        ratingAnimating.value = false;
    }, 700);

    nextTick(() => {
        ratingListRef.value?.$el?.querySelectorAll('.top-classes__class-points').forEach((points) => {
            const leader = points.closest('.top-classes__class')?.dataset.ratingKey;
            const oldWidth = leader ? oldPointsWidths.get(leader) : undefined;
            const newWidth = points.getBoundingClientRect().width;

            if (oldWidth === undefined || Math.abs(newWidth - oldWidth) < 0.5) {
                return;
            }

            points.animate(
                [{ width: `${oldWidth}px` }, { width: `${newWidth}px` }],
                { duration: 650, easing: 'cubic-bezier(0.22, 1, 0.36, 1)' },
            );
        });
    });
}

function showPopularSlide(nextIndex, direction) {
    if (popularAnimating.value || props.popularCards.length < 2) {
        return;
    }

    const count = props.popularCards.length;
    const targetIndex = Math.max(0, Math.min(count - 1, nextIndex));

    if (targetIndex === popularIndex.value) {
        return;
    }

    previousPopularIndex.value = popularIndex.value;
    popularIndex.value = targetIndex;
    popularDirection.value = direction;
    popularAnimating.value = !prefersReducedMotion.value;

    window.clearTimeout(popularAnimationTimer);
    if (popularAnimating.value) {
        popularAnimationTimer = window.setTimeout(() => {
            previousPopularIndex.value = null;
            popularAnimating.value = false;
        }, 450);
    } else {
        previousPopularIndex.value = null;
    }
}

function showPopularSlideMobile(nextIndex, direction) {
    if (popularAnimating.value || popularPendingSwitch.value) {
        return;
    }

    const anyOpen = popularCollapseRefs.value.some(c => c?.isOpen());

    if (anyOpen) {
        popularPendingSwitch.value = { nextIndex, direction };
        popularCollapseRefs.value.forEach(c => c?.close());
        window.clearTimeout(popularSwitchTimer);
        popularSwitchTimer = window.setTimeout(() => {
            const pending = popularPendingSwitch.value;
            popularPendingSwitch.value = null;
            showPopularSlide(pending.nextIndex, pending.direction);
        }, COLLAPSE_ANIMATION_MS);
        return;
    }

    showPopularSlide(nextIndex, direction);
}

function popularSlideClasses(index) {
    if (!popularAnimating.value) {
        return {};
    }

    if (index === popularIndex.value) {
        return {
            'is-entering-from-right': popularDirection.value > 0,
            'is-entering-from-left': popularDirection.value < 0,
        };
    }

    if (index === previousPopularIndex.value) {
        return {
            'is-leaving-to-left': popularDirection.value > 0,
            'is-leaving-to-right': popularDirection.value < 0,
        };
    }

    return {};
}

function updateUpdatesScrollState() {
    const news = updatesNews.value;

    if (!news) {
        return;
    }

    const remainingScroll = news.scrollHeight - news.clientHeight - news.scrollTop;

    updatesAtEnd.value = remainingScroll <= updatesEndThreshold;
}

onMounted(() => {
    nextTick(updateUpdatesScrollState);

    if (isMobile.value) {
        initGridCarousel();
    }

    if (prefersReducedMotion.value || !ratingBlockRef.value || !('IntersectionObserver' in window)) {
        revealRating();
    } else {
        ratingObserver = new IntersectionObserver(([entry], observer) => {
            if (!entry.isIntersecting) {
                return;
            }

            revealRating();
            observer.disconnect();
        }, { threshold: 0.25 });

        ratingObserver.observe(ratingBlockRef.value);
    }
});

onBeforeUnmount(() => {
    destroyGridCarousel();
    window.clearTimeout(ratingAnimationTimer);
    window.clearTimeout(popularSwitchTimer);
    ratingObserver?.disconnect();
    window.clearTimeout(popularAnimationTimer);
});

</script>

<template>

    <div v-if="!isMobile" class="hero">
        <div class="hero__content">
            <div class="hero__title">
                <h1>Фан-сообщество <span style="color: rgb(224, 11, 82);">D</span>COTE</h1>
                <h3>Обитель фанатского комьюнити произведения «Добро пожаловать в класс превосходства».</h3>
            </div>
            <p>{{ heroText }}</p>
            <div class="hero__buttons">
                <a class="link-pill">ЧИТАТЬ НОВОСТИ</a>
                <a :href="route('about-project')" class="btn-pill" style="background-color:rgba(100, 68, 172, 1)">О ПРОЕКТЕ</a>
                <a href="https://t.me/DCOTEFILES" target="_blank" class="btn-pill" style="background-color:rgba(48, 88, 200, 1)" rel="noopener noreferrer">ТЕЛЕГРАМ-КАНАЛ</a>
            </div>
        </div>
        <img class="hero__image" :src="'/images/index/ayano-sakayanagi.webp'" fetchpriority="high" decoding="async" alt="Арису и Аяно" />
    </div>
    <div v-else class="hero hero--mobile">
        <img class="hero__image" :src="'/images/index/ayano-sakayanagi-mobile.webp'" fetchpriority="high" decoding="async" alt="Арису и Аяно" />
        <div class="hero__content">
            <div class="stack">
                <div class="hero__title">
                    <h1>Фан-сообщество <span style="color: rgb(224, 11, 82);">D</span>COTE</h1>
                </div>
                <ClampedText
                    class="hero__text"
                    :text="heroText"
                    :lines="4">
                    <template #button="{ toggle, expanded, id }">
                        <button
                            type="button"
                            class="hero__read-more"
                            :aria-controls="id"
                            :aria-expanded="expanded"
                            @click="toggle">
                            {{ expanded ? 'Свернуть' : 'Читать далее' }}
                        </button>
                    </template>
                </ClampedText>
            </div>
            <div class="hero__buttons">
                <a class="link-pill">ЧИТАТЬ НОВОСТИ</a>
                <a :href="route('about-project')" class="link-pill" rel="noopener noreferrer" style="background-color: rgba(100, 68, 172, 1)">О ПРОЕКТЕ</a>
            </div>
        </div>
    </div>
    <div class="grid-images" ref="gridImagesRef">
        <div class="grid-images__inner">
            <a
                v-for="(card, index) in gridCards"
                :key="card.title"
                :href="card.href"
                class="grid-image-card"
                :class="{ 'is-selected': activeGridIndex === index }"
                @click="onGridImageClick(index, $event)">
                <div class="grid-image-card__scale">
                    <div class="grid-image-card__wrapper">
                        <picture>
                            <source media="(max-width: 768px)" :srcset="card.imgMobile" type="image/webp">
                            <img class="grid-image-card__image" :src="card.img" :alt="card.title">
                        </picture>
                        <div class="grid-image-card__content">
                            <h3 class="grid-image-card__title"><b>{{ card.title }}</b></h3>
                            <p class="grid-image-card__text">{{ card.text }}</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="featured">
        <div class="popular">
            <h1 class="popular__title">ПОПУЛЯРНОЕ</h1>
            <div v-if="isMobile" class="tab-selector">
                <div class="tab-selector__scroll" role="tablist" aria-label="Популярное в DCOTE">
                    <button
                        v-for="(card,index) in popularCards"
                        :key="card.url ?? index"
                        type="button"
                        role="tab"
                        :id="`popular-tab-${index}`"
                        :class="{ 'tab-selector__tab--active': popularIndex === index }"
                        class="tab-selector__tab"
                        :aria-selected="popularIndex === index"
                        :aria-controls="`popular-panel-${index}`"
                        @click="showPopularSlideMobile(index, index > popularIndex ? +1 :-1)"
                    ><b>{{ card.category }}</b> {{ card.title }}</button>
                </div>
            </div>
            <div class="popular__pager">
                <template v-if="!isMobile">
                        <button
                            class="popular__btn"
                            type="button"
                            @click="showPopularSlide(popularIndex - 1, -1)"
                            :disabled="popularIndex === 0 || popularCards.length <= 1"
                            aria-label="Назад">
                        <svg class="popular__slider-icon" width="30" height="30">
                            <use href="#arrow-left"></use>
                        </svg>
                    </button>
                </template>
                <div class="popular__slides">
                    <template v-if="popularCards.length > 0">
                        <div
                            v-for="(card, index) in popularCards"
                            :key="card.url ?? index"
                            class="popular__slide"
                            :class="popularSlideClasses(index)"
                            :hidden="index !== popularIndex && index !== previousPopularIndex"
                            :aria-hidden="index !== popularIndex">
                            <div class="popular__image">
                                <img :src="card.image" loading="lazy" decoding="async" :alt="card.alt">
                            </div>
                            <div class="popular__text">
                                <h3 v-if="!isMobile"><b>{{ card.category }}</b> {{ card.title }}</h3>
                                    <Collapse :always-open="!isMobile" class="popular__details" ref="popularCollapseRefs">
                                        <template #trigger>
                                            <p class="popular__details-title">БАЗОВАЯ ИНФОРМАЦИЯ <svg v-if="isMobile" class="dropdown-icon"><use href="#dropdown" /></svg></p>
                                        </template>
                                        <dl class="popular__info">
                                            <template v-for="item in card.info" :key="item.label">
                                                <dt class="popular__info-label">{{ item.label }}</dt>
                                                <dd class="popular__info-value" :class="item.class || ''">{{ item.value }}</dd>
                                            </template>
                                        </dl>
                                    </Collapse>
                                    <div class="popular__progress">
                                        <p><b>{{ card.progress_label }}: {{ card.progress_current }}</b> из {{ card.progress_total }} {{ card.progress_unit }}</p>
                                        <div class="popular__progress-bar" :style="{ '--progress-width': `${card.progress_percent}%` }"></div>
                                    </div>
                                <a :href="card.url" class="link-pill" rel="noopener noreferrer">
                                    {{card.button_label }}
                                </a>
                            </div>
                        </div>
                    </template>
                    <div v-else class="popular__empty">
                        <p>Пока пусто</p>
                    </div>
                </div>
                <template v-if="!isMobile">
                        <button
                            class="popular__btn"
                            type="button"
                            @click="showPopularSlide(popularIndex + 1, 1)"
                            :disabled="popularIndex === popularCards.length - 1 || popularCards.length <= 1"
                            aria-label="Вперёд">
                        <svg class="popular__slider-icon" width="30" height="30">
                            <use href="#arrow-right"></use>
                        </svg>
                    </button>
                </template>
            </div>
        </div>
        <div class="updates" :class="{ 'is-at-end': updatesAtEnd }">
            <div class="updates__title">
                <h1>ОБНОВЛЕНИЯ</h1>
            </div>
            <div class="updates__viewport">
                <div
                    ref="updatesNews"
                    class="updates__news"
                    @scroll.passive="updateUpdatesScrollState"
                >
                    <div v-for="(update, index) in feed" :key="update.id" class="updates__item">
                        <h3 class="updates__date" :class="{ 'updates__date--hot': index === 0 }">
                            {{ index === 0 ? 'НОВОЕ' : formatDate(update.created_at) }}
                        </h3>
                        <div class="updates__item-news">
                            <a :href="`/${update.link}`" class="updates__item-link" style="line-height: 1.4em">
                                <p class="updates__item-text">{{ update.description }}</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="updates__view-all">
                <a class="link-pill disabled_a" rel="noopener noreferrer">ВСЕ ОБНОВЛЕНИЯ</a>
            </div>
        </div>
    </div>
    <div class="ratings">
        <div ref="ratingBlockRef" class="top-classes">
            <div class="top-classes__title">
                <h1>РЕЙТИНГ КЛАССОВ</h1>
            </div>
            <div class="top-classes__spoiler-cont">
                <button
                    class="top-classes__spoiler-toggle btn-pill-outline"
                    :class="{ 'top-classes__spoiler-toggle--true': showSpoilers }"
                    style="border-color:rgba(68, 44, 97, 1); background-color:rgba(36, 24, 50, 1)"
                    type="button"
                    :aria-pressed="showSpoilers"
                    @click="toggleSpoilers"
                >
                    <span>БЕЗ СПОЙЛЕРОВ</span>
                    <span>СО СПОЙЛЕРАМИ</span>
                </button>
            </div>
            <TransitionGroup
                ref="ratingListRef"
                name="rating"
                tag="div"
                move-class="top-classes__rating-move"
                class="top-classes__rating"
                :class="{
                    'top-classes__rating--intro-ready': true,
                    'is-rating-visible': ratingVisible,
                    'top-classes__rating--intro-complete': ratingIntroComplete,
                }">
                <div v-for="(card, index) in ratingCards" :key="card.slot" class="top-classes__class" :data-rating-key="card.classSchool.leader">
                    <img class="top-classes__class-image" :src="card.classSchool.leader_img" loading="lazy" decoding="async" :alt="card.classSchool.leader">
                    <div class="top-classes__class-info">
                        <div class="top-classes__class-text">
                            <h3>Класс {{ card.classSchool.letter}}</h3>
                            <p>{{ card.classSchool.leader}}</p>
                        </div>
                        <div class="top-classes__class-points"
                            :style="{
                                '--points-width': `${card.classSchool.percent}%`,
                                '--rating-index': index,
                                background: card.classSchool.color}">
                            <p><b>{{card.classSchool.class_points }}</b> очков</p>
                        </div>
                    </div>
                </div>
            </TransitionGroup>
            <div class="full-stat"><button disabled class="btn-pill">ПОЛНАЯ СТАТИСТИКА</button></div>
        </div>
        <div class="top-users">
            <div class="top-users__title">
                <h1>РЕЙТИНГ ПОЛЬЗОВАТЕЛЕЙ</h1>
            </div>
            <div class="top-users__table">
                <table style="height: 100%;">
                    <thead>
                        <tr>
                            <th class="top-users__head-rank" style="width:10%;">
                                <p>Ранг</p>
                            </th>
                            <th class="top-users__head-avatar" style="width:8%;"></th>
                            <th class="top-users__head-nickname" style="width:32%;">
                                <p>Пользователь</p>
                            </th>
                            <th class="top-users__head-status" style="width:35%;">
                                <p>Статус</p>
                            </th>
                            <th class="top-users__head-rating" style="width:14%;">
                                <p>Рейтинг</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="height: 100%;">
                        <tr v-for="user in topUsers" :key="user.rank">
                            <td class="top-users__rank"><svg class="top-users__chess-icon" :style="{ color: user.color }">
                                    <use :href="`#${user.icon}`"></use>
                                </svg>
                                <p><b>{{ user.rank }}</b></p>
                            </td>
                            <td><img class="top-users__avatar" :src="'/images/user-avatar.webp'" alt=""></td>
                            <td class="top-users__nickname" data-status="-">
                                <p>-</p>
                            </td>
                            <td class="top-users__status">
                                <p style="color: white;">-</p>
                            </td>
                            <td class="top-users__rating">
                                <p>{{ user.rating }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="full-stat"><button disabled class="btn-pill">ВСЕ ПОЛЬЗОВАТЕЛИ</button></div>
        </div>
    </div>
    <div class="descriptions">
        <div class="description">
            <div class="description__image">
                <picture>
                    <source media="(max-width: 768px)" :srcset="'/images/index/na-divane-mobile.webp'" type="image/webp">
                    <img :src="'/images/index/na-divane.webp'" alt="На диване">
                </picture>
            </div>
            <div class="description__body">
                <div class="description__content">
                    <h1 class="description__title">ОПИСАНИЕ НОВЕЛЛЫ</h1>
                    <p>
                        Добро пожаловать в класс превосходства — это напряжённая школьная драма
                        с элементами психологического триллера, действие которой разворачивается
                        в престижной государственной школе Кодо Икусэй, известной идеальными условиями
                        и почти гарантированным будущим успехом для выпускников. Однако за внешним
                        совершенством скрывается жестокая система ранжирования, где учащиеся получают
                        всё — от привилегий до денежных баллов — строго по заслугам и результатам конкуренции.
                        <br><br>
                        Главный герой, таинственный и замкнутый Аянокоджи Киётака, по воле обстоятельств
                        оказывается в худшем классе D, куда отправляют «дефективных» учеников школы.
                        Несмотря на намерение оставаться в тени, он постепенно оказывается втянут в
                        сложную игру интриг, стратегий и скрытых конфликтов между учениками школы.
                    </p>
                </div>
                <div class="description__action"><a class="btn-pill disabled_a" rel="noopener noreferrer">БОЛЬШЕ ИНФОРМАЦИИ</a></div>
            </div>
        </div>
        <div class="description">
            <div class="description__image">
                <picture>
                    <source media="(max-width: 768px)" :srcset="'/images/index/shkola-mobile.webp'" type="image/webp">
                    <img :src="'/images/index/shkola.webp'" alt="Школа">
                </picture>
            </div>
            <div class="description__body">
                <div class="description__content">
                    <h1 class="description__title">ОСНОВНОЙ СЕТТИНГ</h1>
                    <p>
                        Токийское государственное учебное учреждение, созданное японским правительством
                        для воспитания молодых выпускников, которые в будущем будут поддерживать различные
                        профессиональные отрасли страны, что подкрепляется особыми методами обучения.
                        За счёт своей репутации, школа может похвастаться своим практически сто процентным уровнем
                        занятости и возможным поступлением в престижный колледж или университет. Сам кампус располагается на отдельном,
                        искусственно сконструированном острове, площадь которого составляет около шестиста тысяч квадратных метров.
                        <br><br>
                        Примечательно, что председателем совета директоров данного учебного заведения является Сакаянаги Нарумори — отец Сакаянаги Арису.
                    </p>
                </div>
                <div class="description__action"><a class="btn-pill" :href="route('about-school')" rel="noopener noreferrer">ПОДРОБНАЯ ИНФОРМАЦИЯ</a></div>
            </div>
        </div>
    </div>
    <div class="island">
        <div class="island__text">
            <h1 class="island__title">ВТОРИЧНЫЙ СЕТТИНГ</h1>
            <p>Необитаемый остров, что юридически принадлежит школе и периодически используется руководством для проведения
                специальных экзаменов.<br><br>
                Проведение таких экзаменов по традиции выпадает на начало нового года и знаменует собой всю серьёзность
                выстроенной правительством школьной системой - выживание в диких условиях, работа в команде, противостояние группам оппонентов.
                <br><br>Сам остров делится на специальные сектора, что предназначены для реального использования на специфичных по
                правилам экзаменах.<br><br>Путешествия на остров происходят каждый год, что позволяет
                выработать у учеников некую адаптацию к подобным условиям.
            </p>
        </div>
        <div class="island__image">
            <picture>
                <source media="(max-width: 768px)" :srcset="'/images/index/остров-mobile.webp'" type="image/webp">
                <img :src="'/images/index/остров.webp'" loading="lazy" decoding="async" alt="Крутой остров фото скачать" />
            </picture>
        </div>
    </div>
    <div class="tg-banner">
        <h1 class="tg-banner__title" style="text-align: center;">МЫ В TELEGRAM</h1>
        <div v-if="isMobile" class="tab-selector">
            <div class="tab-selector__scroll" role="tablist" aria-label="Наши Telegram-каналы">
                <button
                    v-for="badge in tgBadges"
                    :key="badge.id"
                    type="button"
                    role="tab"
                    :id="`tg-tab-${badge.id}`"
                    :class="{ 'tab-selector__tab--active': activeTgTab === badge.id }"
                    class="tab-selector__tab"
                    :aria-selected="activeTgTab === badge.id"
                    :aria-controls="`tg-panel-${badge.id}`"
                    @click="activeTgTab = badge.id"
                >{{ badge.tabTitle }}</button>
            </div>
        </div>
        <div class="tg-banner__badges">
            <div
                v-for="badge in tgBadges"
                :key="badge.id"
                v-show="!isMobile || badge.id === activeTgTab"
                class="tg-banner__badge"
                :id="isMobile ? `tg-panel-${badge.id}` : null"
                :role="isMobile ? 'tabpanel' : null"
                :aria-labelledby="isMobile ? `tg-tab-${badge.id}` : null">
                <h2 class="tg-banner__badge-title">{{ badge.title }}</h2>
                <div class="tg-banner__row">
                    <div class="tg-banner__source">
                        <img class="tg-banner__avatar" :src="badge.avatarChannel" :alt="`Аватар канала «${badge.title}»`" decoding="async">
                        <a :href="badge.channel" style="background-color: rgba(48, 88, 200, 1);" class="link-pill tg-banner__link" target="_blank" rel="noopener noreferrer">КАНАЛ</a>
                    </div>
                    <div class="tg-banner__source">
                        <img class="tg-banner__avatar" :src="badge.avatarChat" :alt="`Аватар беседы «${badge.title}»`" decoding="async">
                        <a :href="badge.chat" style="border-color: rgba(48, 88, 200, 1);" class="link-pill-outline tg-banner__link" target="_blank" rel="noopener noreferrer">БЕСЕДА</a>
                    </div>
                </div>
                <p class="tg-banner__badge-text">{{ badge.text }}</p>
            </div>
        </div>
    </div>
</template>
