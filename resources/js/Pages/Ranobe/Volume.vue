<script setup>
import { computed, defineOptions, ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import '../../../css/pages/ranobe/volume.css';
import '../../../css/components/list-filter.css';
import '../../../css/components/dropdown.css';
import Breadcrumbs from '../../Components/Breadcrumbs.vue';
import RatingWidget from '../../Components/RatingWidget.vue';
import ImageCarousel from '../../Components/ImageCarousel.vue';
import ClampedText from '../../Components/ClampedText.vue';
import { useIsMobile } from '../../Composables/useMediaQuery.js';
import Comments from '../../Pages/Comments.vue';

const props = defineProps({
    year: {
        type: Number,
        required: true,
    },
    volume_number_rounded: {
        type: [Number, String],
        required: true,
    },
    volume: {
        type: Object,
        default: () => ({}),
    },
    chapters: {
        type: Array,
        default: () => [],
    },
    color_images: {
        type: Array,
        default: () => [],
    },
    bw_images: {
        type: Array,
        default: () => [],
    },
});

defineOptions({
    layout: AppLayout,
});

const sortAscending = ref(true);
const filterOpen = ref(false);
const isMobile = useIsMobile();

const volumeDescription = computed(() => {
    const value = (props.volume.description ?? '').trim();

    return !value || value.toLowerCase() === 'null'
        ? `Описание ${props.volume_number_rounded} тома ${props.year} года обучения`
        : value;
});

const sortedChapters = computed(() => {
    const list = [...props.chapters];
    return sortAscending.value ? list : list.reverse();
});
</script>

<template>
    <Breadcrumbs
        :items="[
            { text: 'ГЛАВНАЯ', href: route('home') },
            { text: 'РАНОБЭ', href: route('ranobe.index') },
            { text: `${year} ГОД`, href: route('ranobe.year', { year }) },
            { text: `${volume_number_rounded} ТОМ` },
        ]" />

    <div class="volume-card">
        <div class="volume-card__cover">
            <picture>
                <source media="(max-width: 768px)" :srcset="volume.cover_image_mobile" type="image/webp" />
                <img
                    :src="volume.cover_image"
                    fetchpriority="high"
                    decoding="async"
                    :alt="`Обложка ${volume_number_rounded} тома ${year} года`" />
            </picture>
        </div>
        <div class="volume-card__desc">
            <h1 class="volume-card__title">
                {{ volume_number_rounded }} ТОМ
                <template v-if="Number(volume_number_rounded) !== 0">{{ year }} ГОДА ОБУЧЕНИЯ</template>
            </h1>
            <ClampedText
                class="volume-card__description"
                :text="volumeDescription"
                :lines="6"
                :always-expanded="!isMobile">
                <template #button="{ toggle, expanded, id }">
                    <button
                        type="button"
                        class="volume-card__read-more"
                        :aria-controls="id"
                        :aria-expanded="expanded"
                        @click="toggle">
                        {{ expanded ? 'меньше' : 'больше' }}
                    </button>
                </template>
            </ClampedText>
            <div class="volume-card__actions">
                <button
                    class="btn-pill-outline"
                    style="border-color: rgba(98, 59, 146, 1); gap: var(--fs-gap10)"
                    disabled
                    aria-expanded="false">
                    ДОБАВИТЬ В
                    <svg class="dropdown-icon"><use href="#dropdown" /></svg>
                </button>
                <Link
                    class="link-pill"
                    :href="route('ranobe.chapter', { year, volume: volume_number_rounded, chapter: 1 })"
                    >НАЧАТЬ ЧИТАТЬ</Link
                >
                <a :href="volume.promo_link" class="link-pill-outline" style="border-color: rgba(98, 59, 146, 1)"
                    >ПРОМО ТОМА</a
                >
            </div>
            <div class="volume-card__actions--mobile">
                <Link
                    class="link-pill"
                    :href="route('ranobe.chapter', { year, volume: volume_number_rounded, chapter: 1 })"
                    >НАЧАТЬ ЧИТАТЬ</Link
                >
                <div class="volume-card__actions-group">
                    <button
                        class="btn-pill-outline"
                        style="border-color: rgba(98, 59, 146, 1); gap: var(--fs-gap10)"
                        disabled
                        aria-expanded="false">
                        ДОБАВИТЬ В
                        <svg class="dropdown-icon"><use href="#dropdown" /></svg>
                    </button>
                    <a :href="volume.promo_link" class="link-pill-outline" style="border-color: rgba(98, 59, 146, 1)"
                        >ПРОМО</a
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="volume-info">
        <div class="chapters">
            <div class="chapters__head">
                <button
                    type="button"
                    class="chapters__sort chapters__control btn-pill-outline"
                    style="border-color: rgba(98, 59, 146, 1)"
                    aria-label="Сортировать по возрастанию/убыванию"
                    @click="sortAscending = !sortAscending">
                    <svg v-show="!sortAscending" class="chapters__sort-icon" aria-hidden="true">
                        <use href="#sort-descending-filled-compact" />
                    </svg>
                    <svg v-show="sortAscending" class="chapters__sort-icon" aria-hidden="true">
                        <use href="#sort-ascending-filled-compact" />
                    </svg>
                    <span>СОРТИРОВКА</span>
                </button>
                <h1 class="chapters__title">ОГЛАВЛЕНИЕ</h1>
                <div
                    class="list-filter chapters__filter"
                    data-list-filter="chapters"
                    :class="{ 'is-open': filterOpen }">
                    <button
                        type="button"
                        class="filter-toggle chapters__control btn-pill-outline"
                        style="border-color: rgba(98, 59, 146, 1)"
                        aria-label="Фильтр глав"
                        :aria-expanded="filterOpen"
                        aria-haspopup="listbox"
                        aria-controls="chapter-filter-menu"
                        @click="filterOpen = !filterOpen">
                        <svg class="chapters__filter-icon" aria-hidden="true">
                            <use href="#filter-filled" />
                        </svg>
                        <span>ФИЛЬТР</span>
                        <svg class="dropdown-icon" aria-hidden="true">
                            <use href="#dropdown" />
                        </svg>
                    </button>
                    <div
                        id="chapter-filter-menu"
                        class="list-filter-menu"
                        role="listbox"
                        aria-label="Критерий сортировки глав">
                        <button type="button" class="list-filter-option is-selected" role="option" aria-selected="true">
                            <svg class="list-filter-option-icon list-filter-option-icon--chapters" aria-hidden="true">
                                <use href="#side-menu-ranobe" />
                            </svg>
                            <span>По главам</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="chapters__rating">
                <RatingWidget
                    rateable-type="ranobe_volume"
                    :rateable-id="volume.id"
                    :user-rating="volume.volume_user_rating"
                    :avg-rating="volume.volume_avg_rating"
                    :ratings-count="volume.volume_ratings_count" />
            </div>
            <div class="chapters__list">
                <Link
                    v-for="chapter in sortedChapters"
                    :key="chapter.id"
                    class="link-pill-outline chapters__chapter"
                    style="border-color: rgba(98, 59, 146, 1)"
                    :href="
                        route('ranobe.chapter', {
                            year,
                            volume: volume_number_rounded,
                            chapter: chapter.chapter_number,
                        })
                    ">
                    <span class="chapters__chapter-title"
                        ><strong v-if="chapter.title_label">{{ chapter.title_label }}</strong
                        >{{ chapter.title ? ' ' + chapter.title : '' }}</span
                    >
                </Link>
            </div>
        </div>

        <div class="volume-gallery">
            <h1 class="volume-gallery__title">ИЛЛЮСТРАЦИИ К ТОМУ</h1>
            <div class="volume-gallery__carousels">
                <div class="volume-gallery__carousel">
                    <h3>ЦВЕТНЫЕ ВЕРСИИ</h3>
                    <ImageCarousel :images="color_images" />
                </div>
                <div class="volume-gallery__carousel">
                    <h3>ЧЁРНО-БЕЛЫЕ ВЕРСИИ</h3>
                    <ImageCarousel :images="bw_images" />
                </div>
            </div>
        </div>
    </div>
    <Comments
        :comment-label="`К ${volume_number_rounded} ТОМУ`"
        commentable-type="ranobe_volume"
        :commentable-id="volume.id" />
</template>
