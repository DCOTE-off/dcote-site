<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import Breadcrumbs from '../../Components/Breadcrumbs.vue';
import ReaderSettings from '../../Components/ReaderSettings.vue';
import { useReadingSettings } from '../../Composables/useReadingSettings.js';
import '../../../css/pages/ranobe/chapter.css';
import '../../../css/pages/reading-settings.css';
import Comments from '../../Pages/Comments.vue';

const props = defineProps({
    year: {
        type: Number,
        required: true,
    },
    volume: {
        type: [Number, String],
        required: true,
    },
    chapter: {
        type: [Number, String],
        required: true,
    },
    titleLabel: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        default: '',
    },
    contentHtml: {
        type: String,
        default: '',
    },
    prevLink: {
        type: String,
        default: null,
    },
    nextLink: {
        type: String,
        default: null,
    },
    chapterId: {
        type: Number,
        required: true,
    },
});

defineOptions({
    layout: AppLayout,
});

const NAV_STATE_KEY = 'dcote-nav-hidden';
const PROGRESS_KEY = 'dcote-reading-progress';

const page = usePage();
const { settings } = useReadingSettings();

const settingsOpen = ref(false);

function readInitialNavHidden() {
    try {
        return JSON.parse(window.localStorage.getItem(NAV_STATE_KEY)) === true;
    } catch {
        return false;
    }
}

const initialNavHidden = readInitialNavHidden();

// Ставим класс до рендера — иначе read-nav (с transition) «уезжает» при загрузке.
if (initialNavHidden) {
    document.documentElement.classList.add('nav-hidden');
}

const navVisible = ref(!initialNavHidden);

function toggleSettingsPanel() {
    settingsOpen.value = !settingsOpen.value;
}

let lastScrollY = 0;
let saveTimer = null;

function setNavVisible(visible) {
    navVisible.value = visible;
    document.documentElement.classList.toggle('nav-hidden', !visible);

    try {
        window.localStorage.setItem(NAV_STATE_KEY, JSON.stringify(!visible));
    } catch {
        // Навигация работает и без сохранения состояния.
    }
}

function saveProgress() {
    try {
        window.localStorage.setItem(
            PROGRESS_KEY,
            JSON.stringify({
                pathname: window.location.pathname,
                scrollY: window.scrollY,
            }),
        );
    } catch {
        // Сохранение позиции необязательно.
    }
}

function restoreProgress() {
    try {
        const raw = window.localStorage.getItem(PROGRESS_KEY);

        if (!raw) {
            return;
        }

        const progress = JSON.parse(raw);
        const scrollY = Number.parseInt(progress?.scrollY, 10);

        if (progress?.pathname === window.location.pathname && Number.isFinite(scrollY) && scrollY > 0) {
            window.scrollTo(0, scrollY);
        }
    } catch {
        // Повреждённые данные не мешают чтению.
    }
}

function scheduleSaveProgress() {
    window.clearTimeout(saveTimer);
    saveTimer = window.setTimeout(saveProgress, 300);
}

function onScroll() {
    const delta = window.scrollY - lastScrollY;

    if (delta > 5 && navVisible.value && !settingsOpen.value) {
        setNavVisible(false);
    }

    lastScrollY = window.scrollY;
    scheduleSaveProgress();
}

function onContentClick() {
    if (settingsOpen.value) {
        settingsOpen.value = false;
    }

    setNavVisible(!navVisible.value);
}

function syncPageState() {
    setNavVisible(true);
    restoreProgress();
    window.setTimeout(restoreProgress, 300);
    lastScrollY = window.scrollY;
}

onMounted(() => {
    restoreProgress();
    window.setTimeout(restoreProgress, 300);
    window.addEventListener('load', restoreProgress);
    window.addEventListener('scroll', onScroll, { passive: true });
    lastScrollY = window.scrollY;
});

onBeforeUnmount(() => {
    window.clearTimeout(saveTimer);
    window.removeEventListener('load', restoreProgress);
    window.removeEventListener('scroll', onScroll);
});

watch(() => page.url, syncPageState);
</script>

<template>
    <Breadcrumbs
        v-show="settings.navigation"
        :items="[
            { text: 'ГЛАВНАЯ', href: route('home') },
            { text: 'РАНОБЭ', href: route('ranobe.index') },
            { text: `${year} ГОД`, href: route('ranobe.year', { year }) },
            { text: `${volume} ТОМ`, href: route('ranobe.volume', { year, volume }) },
            { text: `${chapter} ГЛАВА` },
        ]" />

    <div class="chapter-container">
        <h3 v-show="settings.title" class="main-title">
            <strong>{{ titleLabel }}</strong
            >{{ title ? ' ' + title : '' }}
        </h3>
        <article
            class="chapter-content"
            :class="{ 'hide-images': !settings.images }"
            @click="onContentClick"
            v-html="contentHtml"></article>
        <ReaderSettings :settings="settings" :open="settingsOpen" />
        <div class="read-nav desktop">
            <button type="button" class="read-item">
                <svg class="nav-icon" viewBox="0 0 11 15"><use href="#mark" /></svg>
            </button>
            <Link class="read-item" :href="route('ranobe.volume', { year, volume })">
                <svg class="nav-icon" viewBox="0 0 17 15"><use href="#list-details" /></svg>
            </Link>
            <button type="button" class="read-item settingsReadBtn" @click="toggleSettingsPanel">
                <svg class="nav-icon" viewBox="0 0 25 25"><use href="#settings" /></svg>
            </button>
        </div>
        <div class="read-nav mobile">
            <component
                :is="prevLink ? Link : 'a'"
                class="read-item"
                :class="{ disabled: !prevLink }"
                :href="prevLink || undefined">
                <svg v-if="prevLink" class="arrow" viewBox="0 0 12 8">
                    <use href="#mini-arrow-left" />
                </svg>
            </component>
            <div class="center-items">
                <Link class="read-item" :href="route('ranobe.volume', { year, volume })">
                    <svg class="nav-icon" viewBox="0 0 17 15"><use href="#list-details" /></svg>
                </Link>
                <button type="button" class="read-item">
                    <svg class="nav-icon" viewBox="0 0 11 15"><use href="#mark" /></svg>
                </button>
                <button type="button" class="read-item settingsReadBtn" @click="toggleSettingsPanel">
                    <svg class="nav-icon" viewBox="0 0 25 25"><use href="#settings" /></svg>
                </button>
            </div>
            <component
                :is="nextLink ? Link : 'a'"
                class="read-item"
                :class="{ disabled: !nextLink }"
                :href="nextLink || undefined">
                <svg v-if="nextLink" class="arrow" viewBox="0 0 12 8">
                    <use href="#mini-arrow-right" />
                </svg>
            </component>
        </div>
    </div>

    <div class="chapters-controls">
        <component
            :is="prevLink ? Link : 'a'"
            class="link-pill prev-episode-btn"
            :class="{ disabled: !prevLink }"
            :href="prevLink || undefined">
            ПРЕДЫДУЩАЯ ГЛАВА
        </component>
        <Link
            class="link-pill-outline"
            style="border-color: rgba(146, 21, 69, 1)"
            :href="route('ranobe.volume', { year, volume })"
            >ВСЕ ГЛАВЫ</Link
        >
        <component
            :is="nextLink ? Link : 'a'"
            class="link-pill next-episode-btn"
            :class="{ disabled: !nextLink }"
            :href="nextLink || undefined">
            СЛЕДУЮЩАЯ ГЛАВА
        </component>
    </div>
    <div class="chapters-controls mobile">
        <component
            :is="prevLink ? Link : 'a'"
            class="chapter-control-item"
            :class="{ disabled: !prevLink }"
            :href="prevLink || undefined">
            <svg v-if="prevLink" class="arrow" viewBox="0 0 12 8">
                <use href="#mini-arrow-left" />
            </svg>
        </component>
        <Link class="chapter-control-item" :href="route('ranobe.volume', { year, volume })">
            <svg class="nav-icon" viewBox="0 0 17 15"><use href="#list-details" /></svg>
        </Link>
        <component
            :is="nextLink ? Link : 'a'"
            class="chapter-control-item"
            :class="{ disabled: !nextLink }"
            :href="nextLink || undefined">
            <svg v-if="nextLink" class="arrow" viewBox="0 0 12 8">
                <use href="#mini-arrow-right" />
            </svg>
        </component>
    </div>
    <Comments :comment-label="`К ${chapter} ГЛАВЕ`" commentable-type="ranobe_chapter" :commentable-id="chapterId" />
</template>
