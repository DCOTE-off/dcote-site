import { onBeforeUnmount, reactive, watch } from 'vue';

const STORAGE_KEY = 'dcote-reading-settings';

export const FONT_MAP = {
    'Vag Rounded Next': "'Vag Rounded Next', sans-serif",
    Nunito: "'Nunito', sans-serif",
    'Times New Roman': "'Times New Roman', Georgia, serif",
    'Open Sans': "'Open Sans', sans-serif",
};

export const THEME_MAP = {
    Стандартная: { text: 'rgba(244, 239, 250, 1)', bg: 'rgb(14, 10, 21)' },
    Legacy: { text: '#e9e9e9', bg: 'rgb(7, 18, 32)' },
    Тёмная: { text: '#bfbfbf', bg: '#0a0a0a' },
    Серая: { text: '#dbdbdb', bg: '#434751' },
    Светлая: { text: '#212529', bg: '#f2f2f3' },
    Книжная: { text: '#262425', bg: '#e5cf9d' },
};

const CSS_PROPS = [
    '--font-size-baze',
    '--block-padding',
    '--text-line-height',
    '--text-intend',
    '--cont-width',
    '--navigation-display',
    '--font-family',
    '--primary-text-color',
    '--body-bg-color',
];

function defaultSettings() {
    const isMobile = window.innerWidth < 768;

    return {
        indent: true,
        images: true,
        title: true,
        navigation: true,
        fontSize: isMobile ? 16 : 18,
        lineHeight: 1.6,
        paragraphGap: 10,
        contWidth: isMobile ? 95 : 73,
        fontFamily: 'Vag Rounded Next',
        theme: 'Стандартная',
    };
}

function loadSettings() {
    try {
        const raw = window.localStorage.getItem(STORAGE_KEY);

        return raw ? { ...defaultSettings(), ...JSON.parse(raw) } : defaultSettings();
    } catch {
        return defaultSettings();
    }
}

function persistSettings(settings) {
    try {
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
    } catch {
        // Чтение работает даже при заблокированном хранилище.
    }
}

function applySettings(settings) {
    const root = document.documentElement;
    root.style.setProperty('--font-size-baze', `${settings.fontSize}px`);
    root.style.setProperty('--block-padding', `${settings.paragraphGap / 2}px`);
    root.style.setProperty('--text-line-height', String(settings.lineHeight));
    root.style.setProperty('--text-intend', settings.indent ? '0.875em' : '0');
    root.style.setProperty('--cont-width', `${settings.contWidth}%`);
    root.style.setProperty('--navigation-display', settings.navigation ? 'flex' : 'none');
    root.style.setProperty('--font-family', FONT_MAP[settings.fontFamily] ?? FONT_MAP['Vag Rounded Next']);
    root.style.setProperty('--primary-text-color', THEME_MAP[settings.theme]?.text ?? '#e9e9e9');
    root.style.setProperty('--body-bg-color', THEME_MAP[settings.theme]?.bg ?? 'rgb(7, 18, 32)');
}

function resetSettings() {
    const root = document.documentElement;
    CSS_PROPS.forEach((property) => root.style.removeProperty(property));
    root.classList.remove('nav-hidden');
    root.classList.remove('reader-page');
}

export function useReadingSettings() {
    const settings = reactive(loadSettings());

    // Класс-скоуп для правил читалки (body/main/:root в chapter.css).
    document.documentElement.classList.add('reader-page');

    // Применяем сразу в setup — до отрисовки страницы (анти-FOUC).
    applySettings(settings);

    watch(settings, () => {
        persistSettings(settings);
        applySettings(settings);
    });

    // В SPA глобальные переменные иначе протекут на другие страницы.
    onBeforeUnmount(resetSettings);

    return { settings };
}
