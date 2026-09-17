<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import EmblaCarousel from 'embla-carousel';
import '../../css/components/image-carousel-modal.css';

const props = defineProps({
    images: {
        type: Array,
        default: () => [],   // [{ url, download_url, alt? }]
    },
});

const rootRef = ref(null);
let carousel = null;
let settling = false;

const ready = ref(false);
const selectedIndex = ref(0);

const isModalOpen = ref(false);
const modalIndex = ref(0);

const currentImage = computed(() => props.images[modalIndex.value] ?? null);
const currentDownloadUrl = computed(() => currentImage.value?.download_url || currentImage.value?.url || '');
const currentCaption = computed(() => currentImage.value?.alt || '');

const TAIL_THRESHOLD_PX = 0.5;

function waitForImages(images) {
    return Promise.all(images.map((image) => {
        if (image.complete && image.naturalWidth > 0) {
            return Promise.resolve();
        }

        if (typeof image.decode === 'function') {
            return image.decode().catch(() => {});
        }

        return new Promise((resolve) => {
            image.addEventListener('load', resolve, { once: true });
            image.addEventListener('error', resolve, { once: true });
        });
    }));
}

async function setupCarousel() {
    const root = rootRef.value;
    if (!root) {
        return;
    }

    const images = Array.from(root.querySelectorAll('.carousel-img'));
    if (images.length === 0) {
        return;
    }

    await waitForImages(images);

    if (!rootRef.value) {
        return;
    }

    carousel = EmblaCarousel(root, {
        align: 'center',
        containScroll: false,
        loop: props.images.length > 1,
        slidesToScroll: 1,
        skipSnaps: false,
        watchDrag: () => !settling,
    });

    function updateSelected() {
        selectedIndex.value = carousel.selectedScrollSnap();
    }

    carousel.on('select', updateSelected);
    carousel.on('reInit', updateSelected);
    carousel.on('settle', () => {
        settling = false;
    });

    // Обрезка невидимого хвоста: когда до цели осталось меньше X px —
    // мгновенно доезжаем (jump), чтобы анимация не «ползла» бесконечно.
    carousel.on('scroll', () => {
        const engine = carousel.internalEngine();
        if (engine.dragHandler.pointerDown()) {
            return;
        }
        const remaining = Math.abs(engine.target.get() - engine.location.get());
        if (remaining < TAIL_THRESHOLD_PX) {
            carousel.scrollTo(engine.index.get(), true);
        }
    });

    updateSelected();

    requestAnimationFrame(() => {
        ready.value = true;
    });
}

function onSlideClick(index, event) {
    if (settling) {
        event.preventDefault();
        return;
    }

    if (index === selectedIndex.value) {
        openModal(index);
        return;
    }

    event.preventDefault();

    if (!carousel) {
        return;
    }

    const root = rootRef.value;
    const rootCenter = root.getBoundingClientRect().left + root.offsetWidth / 2;
    const targetCenter = event.currentTarget.getBoundingClientRect().left + event.currentTarget.offsetWidth / 2;
    const goingNext = targetCenter > rootCenter;

    if (goingNext ? !carousel.canScrollNext() : !carousel.canScrollPrev()) {
        return;
    }

    if (goingNext) {
        carousel.scrollNext();
    } else {
        carousel.scrollPrev();
    }

    settling = true;
}

function openModal(index) {
    if (props.images.length === 0) {
        return;
    }

    modalIndex.value = (index + props.images.length) % props.images.length;
    isModalOpen.value = true;
}

function closeModal() {
    isModalOpen.value = false;
}

function showNext() {
    modalIndex.value = (modalIndex.value + 1) % props.images.length;
}

function showPrev() {
    modalIndex.value = (modalIndex.value - 1 + props.images.length) % props.images.length;
}

function onKeydown(event) {
    if (!isModalOpen.value) {
        return;
    }

    if (event.key === 'Escape') {
        closeModal();
    } else if (event.key === 'ArrowRight') {
        showNext();
    } else if (event.key === 'ArrowLeft') {
        showPrev();
    }
}

onMounted(() => {
    setupCarousel();
    document.addEventListener('keydown', onKeydown);
});

onBeforeUnmount(() => {
    carousel?.destroy();
    carousel = null;
    settling = false;
    document.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <div class="carousel-container">
        <div ref="rootRef" class="embla" :class="{ 'is-ready': ready }">
            <div class="embla__container">
                <div
                    v-for="(image, index) in images"
                    :key="image.url"
                    class="embla__slide"
                    :class="{ 'is-selected': index === selectedIndex }"
                    @click="onSlideClick(index, $event)">
                    <img :src="image.url" class="carousel-img" :data-download="image.download_url || image.url" :alt="image.alt || ''">
                </div>
            </div>
        </div>
    </div>

    <div class="modal" data-caption-from-alt :style="{ display: isModalOpen ? 'flex' : 'none' }">
        <span class="modal-close" aria-label="Закрыть окно" @click="closeModal">
            <img :src="'/svgs/close.svg'" alt="Закрыть">
        </span>
        <a class="modal-download" :href="currentDownloadUrl" download aria-label="Скачать изображение">
            <img :src="'/svgs/download.svg'" alt="Скачать">
        </a>
        <span class="modal-next" aria-label="Следующее изображение" @click="showNext">
            <img :src="'/svgs/caret-right.svg'" alt="Следующий">
        </span>
        <span class="modal-prev" aria-label="Предыдущее изображение" @click="showPrev">
            <img :src="'/svgs/caret-left.svg'" alt="Предыдущий">
        </span>
        <img class="modal-content" :src="currentImage?.url" :alt="currentCaption">
        <div class="modal-caption">{{ currentCaption }}</div>
        <div class="modal-overlay" @click="closeModal"></div>
    </div>
</template>
