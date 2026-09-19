<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import '../../css/components/rating.css';
import { ratingStore } from '../stores/rating';

const props = defineProps({
    rateableType: String,
    rateableId: [Number, String],
    userRating: {
        type: Number,
        default: 0,
    },
    avgRating: {
        type: Number,
        default: 0,
    },
    ratingsCount: {
        type: Number,
        default: 0,
    },
    visualOnly: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    noExtra: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const widgetEl = ref(null);
const widgetId = computed(() => `${props.rateableType}:${props.rateableId}`);
const isOpen = computed(() => ratingStore.openId === widgetId.value);

const userRating = ref(props.userRating);
const avgRating = ref(props.avgRating);
const ratingsCount = ref(props.ratingsCount);
const isRated = computed(() => userRating.value > 0);

const hover = ref(0);
const updated = ref(false);
const changing = ref(false);
const direction = ref('up');
const previousValue = ref('');
const nextValue = ref('');

const STARS = 10;

function togglePopup() {
    ratingStore.openId = ratingStore.openId === widgetId.value ? null : widgetId.value;
}

function closePopup() {
    ratingStore.openId = null;
}

function onDocumentClick(event) {
    if (widgetEl.value && !widgetEl.value.contains(event.target) && isOpen.value) {
        closePopup();
    }
}

onMounted(() => document.addEventListener('click', onDocumentClick));
onBeforeUnmount(() => document.removeEventListener('click', onDocumentClick));

function animateRatingIcon() {
    updated.value = false;
    nextTick(() => {
        updated.value = true;
    });
}

function animateRatingValue(previous, next, increasing) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        // Без анимации сразу показываем новое значение. Number() обязателен:
        // сюда приходит строка ("7.5"), а в шаблоне вызывается avgRating.toFixed().
        avgRating.value = Number(next);
        return;
    }

    previousValue.value = previous;
    nextValue.value = next;
    direction.value = increasing ? 'up' : 'down';
    changing.value = true;
}

function finishRatingChange() {
    changing.value = false;
    avgRating.value = Number(nextValue.value);
}

async function submitRating(value) {
    const csrfToken = page.props.csrf_token;
    if (!csrfToken) {
        return;
    }

    animateRatingIcon();

    const method = value ? 'POST' : 'DELETE';
    const body = value
        ? { rateable_type: props.rateableType, rateable_id: props.rateableId, rating: value }
        : { rateable_type: props.rateableType, rateable_id: props.rateableId };

    try {
        const response = await fetch('/api/ratings', {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(body),
        });
        const data = await response.json();

        const previousAverage = avgRating.value;
        const nextAverage = Number(data.avg_rating ?? 0);

        userRating.value = Number(data.user_rating ?? 0);

        if (data.ratings_count !== undefined) {
            ratingsCount.value = Number(data.ratings_count);
        }

        if (previousAverage !== nextAverage) {
            animateRatingValue(previousAverage.toFixed(1), nextAverage.toFixed(1), nextAverage > previousAverage);
        } else {
            avgRating.value = nextAverage;
        }
    } catch {
        // молча игнорируем сетевые ошибки
    }
}

function onStarClick(value) {
    submitRating(value === userRating.value ? 0 : value);
}
</script>

<template>
    <div
        ref="widgetEl"
        class="rating-widget"
        :class="{ 'is-disabled': disabled }"
        :data-rateable-type="rateableType"
        :data-rateable-id="rateableId">
        <div class="star-and-avg">
            <svg v-if="disabled" class="star-rating-icon" viewBox="0 0 36 35" aria-hidden="true">
                <use href="#star" />
            </svg>
            <svg v-else-if="visualOnly" class="star-rating-icon visual" viewBox="0 0 36 35">
                <use href="#star" />
            </svg>
            <button
                v-else
                type="button"
                class="toggle-rating-menu-btn"
                :class="{ active: isOpen, 'has-rating': isRated, 'is-rating-updated': updated }"
                aria-label="Поставить оценку"
                @click.stop="togglePopup"
                @animationend="updated = false">
                <svg class="star-rating-icon" viewBox="0 0 36 35">
                    <use href="#star" />
                </svg>
            </button>

            <h3
                class="rating-value"
                :class="{ 'is-changing': changing }"
                :data-direction="direction">
                <template v-if="changing">
                    <span class="rating-value-current">{{ previousValue }}</span>
                    <span class="rating-value-next" @animationend="finishRatingChange">{{ nextValue }}</span>
                </template>
                <template v-else>{{ avgRating.toFixed(1) }}</template>
            </h3>

            <div v-if="!disabled && !visualOnly" class="rating-popup-menu" :class="{ 'is-open': isOpen }">
                <div class="stars-row" @mouseleave="hover = 0">
                    <button
                        v-for="n in STARS"
                        :key="n"
                        type="button"
                        class="star-value-button"
                        :class="{ picked: n === (hover || userRating) }"
                        @mouseenter="hover = n"
                        @click="onStarClick(n)">
                        <svg class="star-rating-icon star-value-icon" :class="{ filled: n <= (hover || userRating) }" viewBox="0 0 36 35">
                            <use href="#star" />
                        </svg>
                    </button>
                </div>
                <div class="rating-popup-info">
                    <p>Всего оценок: <b class="ratings-count">{{ ratingsCount }}</b></p>
                    <p v-if="isAuthenticated">Ваша оценка: <b class="your-choosen-rating">{{ userRating || '' }}</b></p>
                </div>
                <div v-if="!isAuthenticated" class="popup-overlay">
                    <Link :href="route('login')">
                        <b><p>Войдите в аккаунт, чтобы поставить оценку</p></b>
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="!noExtra" class="extra-info">
            <p>Всего оценок: <span class="ratings-count">{{ ratingsCount }}</span></p>
        </div>
    </div>
</template>