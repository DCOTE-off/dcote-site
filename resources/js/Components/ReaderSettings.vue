<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { FONT_MAP } from '../Composables/useReadingSettings.js';
import '../../css/components/dropdown-select.css';
import '../../css/pages/reading-settings.css';

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
    open: {
        type: Boolean,
        default: false,
    },
});

const fontOptions = Object.keys(FONT_MAP);
const themeOptions = [
    { label: 'Стандартная', id: 'standart-theme' },
    { label: 'Legacy', id: 'legacy-theme' },
    { label: 'Тёмная', id: 'dark-theme' },
    { label: 'Серая', id: 'grey-theme' },
    { label: 'Светлая', id: 'light-theme' },
    { label: 'Книжная', id: 'book-theme' },
];

const openDropdown = ref(null);
const touched = ref(false);

// `not-open` (slideDown) включаем только после первого изменения — иначе панель
// мигает при загрузке страницы.
watch(
    () => props.open,
    () => {
        touched.value = true;
    },
);

function toggleDropdown(name) {
    openDropdown.value = openDropdown.value === name ? null : name;
}

function selectOption(name, value) {
    props.settings[name] = value;
    openDropdown.value = null;
}

function onDocumentClick(event) {
    if (!event.target.closest('.dropdown-select-wrapper')) {
        openDropdown.value = null;
    }
}

onMounted(() => document.addEventListener('click', onDocumentClick));
onBeforeUnmount(() => document.removeEventListener('click', onDocumentClick));
</script>

<template>
    <div class="read-settings" :class="{ 'is-open': open, 'not-open': !open && touched }">
        <div class="text-and-checkbox">
            <span>Отступ:</span>
            <div class="settings-btn-cont">
                <label class="settings-btn">
                    <input v-model="settings.indent" type="checkbox" class="settings-switch" aria-label="Отступ" />
                    <span class="settings-btn__track"></span>
                </label>
            </div>
        </div>
        <div class="text-and-checkbox">
            <span>Изображения:</span>
            <div class="settings-btn-cont">
                <label class="settings-btn">
                    <input v-model="settings.images" type="checkbox" class="settings-switch" aria-label="Изображения" />
                    <span class="settings-btn__track"></span>
                </label>
            </div>
        </div>
        <div class="text-and-checkbox">
            <span>Название главы:</span>
            <div class="settings-btn-cont">
                <label class="settings-btn">
                    <input
                        v-model="settings.title"
                        type="checkbox"
                        class="settings-switch"
                        aria-label="Название главы" />
                    <span class="settings-btn__track"></span>
                </label>
            </div>
        </div>
        <div class="text-and-checkbox">
            <span>Навигация сайта:</span>
            <div class="settings-btn-cont">
                <label class="settings-btn">
                    <input
                        v-model="settings.navigation"
                        type="checkbox"
                        class="settings-switch"
                        aria-label="Навигация сайта" />
                    <span class="settings-btn__track"></span>
                </label>
            </div>
        </div>
        <div class="label-and-range">
            <label
                ><span>Размер шрифта:</span>
                <strong
                    ><span>{{ settings.fontSize }}</span
                    ><span>px</span></strong
                ></label
            >
            <input v-model.number="settings.fontSize" type="range" min="8" max="40" step="1" />
        </div>
        <div class="label-and-range">
            <label
                ><span>Высота строк:</span>
                <strong
                    ><span>{{ Number(settings.lineHeight).toFixed(1) }}</span></strong
                ></label
            >
            <input v-model.number="settings.lineHeight" type="range" min="1" max="2.4" step="0.1" />
        </div>
        <div class="label-and-range">
            <label
                ><span>Отступ между абзацами:</span>
                <strong
                    ><span>{{ settings.paragraphGap }}</span
                    ><span>px</span></strong
                ></label
            >
            <input v-model.number="settings.paragraphGap" type="range" min="5" max="45" step="1" />
        </div>
        <div class="label-and-range">
            <label
                ><span>Ширина контейнера:</span>
                <strong
                    ><span>{{ settings.contWidth }}</span
                    ><span>%</span></strong
                ></label
            >
            <input v-model.number="settings.contWidth" type="range" min="1" max="100" step="1" />
        </div>
        <div class="dd-buttons">
            <div class="dropdown-select-wrapper">
                <button
                    type="button"
                    class="dropdown-select-btn btn-pill-outline"
                    style="border-color: rgba(98, 59, 146, 1)"
                    :class="{ 'is-open': openDropdown === 'font' }"
                    :aria-expanded="openDropdown === 'font'"
                    @click.stop="toggleDropdown('font')">
                    ШРИФТ
                    <svg class="dropdown-icon"><use href="#dropdown" /></svg>
                </button>
                <div id="fontDdContent" class="dropdown-content" :class="{ 'is-open': openDropdown === 'font' }">
                    <button
                        v-for="font in fontOptions"
                        :key="font"
                        type="button"
                        class="dropdown-list-value"
                        :class="{ selected: settings.fontFamily === font }"
                        @click="selectOption('fontFamily', font)">
                        {{ font }}
                        <span class="check-mark-bg"
                            ><svg class="check-mark-icon"><use href="#check-mark" /></svg
                        ></span>
                    </button>
                </div>
            </div>
            <div class="dropdown-select-wrapper">
                <button
                    type="button"
                    class="dropdown-select-btn btn-pill-outline"
                    style="border-color: rgba(98, 59, 146, 1)"
                    :class="{ 'is-open': openDropdown === 'theme' }"
                    :aria-expanded="openDropdown === 'theme'"
                    @click.stop="toggleDropdown('theme')">
                    ТЕМА
                    <svg class="dropdown-icon"><use href="#dropdown" /></svg>
                </button>
                <div id="themeDdContent" class="dropdown-content" :class="{ 'is-open': openDropdown === 'theme' }">
                    <button
                        v-for="theme in themeOptions"
                        :id="theme.id"
                        :key="theme.label"
                        type="button"
                        class="dropdown-list-value"
                        :class="{ selected: settings.theme === theme.label }"
                        @click="selectOption('theme', theme.label)">
                        {{ theme.label }}
                        <span class="check-mark-bg"
                            ><svg class="check-mark-icon"><use href="#check-mark" /></svg
                        ></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
