document.addEventListener('DOMContentLoaded', () => {
    const STORAGE_KEY = 'dcote-reading-settings';
    const NAV_STATE_KEY = 'dcote-nav-hidden';
    const PROGRESS_KEY = 'dcote-reading-progress';

    const root = document.documentElement;
    const readSettings = document.querySelector('.read-settings');
    const chapterContainer = document.querySelector('.chapter-container');
    const chapterContent = document.querySelector('.chapter-content');
    const chapterTitle = document.querySelector('.main-title');

    const controls = {
        indent: document.getElementById('indentCheckbox'),
        images: document.getElementById('imagesCheckbox'),
        title: document.getElementById('titleCheckbox'),
        navigation: document.getElementById('navigationCheckbox'),
        fontSize: document.getElementById('fontSize'),
        lineHeight: document.getElementById('lineHeight'),
        paragraphGap: document.getElementById('paragraphGap'),
        contWidth: document.getElementById('contWidth'),
    };

    const valueOutputs = {
        fontSize: document.getElementById('fontSizeValue'),
        lineHeight: document.getElementById('lineHeightValue'),
        paragraphGap: document.getElementById('paragraphGapValue'),
        contWidth: document.getElementById('contWidthValue'),
    };

    const fontMap = {
        'Vag Rounded Next': "'Vag Rounded Next', sans-serif",
        'Times New Roman': "'Times New Roman', Georgia, serif",
        'Open Sans': "'Open Sans', sans-serif",
    };

    const themeMap = {
        'Стандартная': { text: '#e9e9e9', bg: 'rgb(7, 18, 32)' },
        'Тёмная': { text: '#bfbfbf', bg: '#0a0a0a' },
        'Серая': { text: '#dbdbdb', bg: '#434751' },
        'Светлая': { text: '#212529', bg: '#f2f2f3' },
        'Книжная': { text: '#262425', bg: '#e5cf9d' },
    };

    const isMobile = window.innerWidth < 768;
    const defaultSettings = {
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

    function loadSettings() {
        try {
            const savedSettings = window.localStorage.getItem(STORAGE_KEY);
            return savedSettings
                ? { ...defaultSettings, ...JSON.parse(savedSettings) }
                : { ...defaultSettings };
        } catch {
            return { ...defaultSettings };
        }
    }

    function saveSettings(settings) {
        try {
            window.localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
        } catch {
            // Чтение должно работать, даже если хранилище заблокировано или заполнено.
        }
    }

    function setControlValue(name, value) {
        if (controls[name]) {
            controls[name].value = value;
        }

        if (valueOutputs[name]) {
            valueOutputs[name].textContent = value;
        }
    }

    function applySettings(settings) {
        ['indent', 'images', 'title', 'navigation'].forEach((name) => {
            if (controls[name]) {
                controls[name].checked = settings[name];
            }
        });

        setControlValue('fontSize', settings.fontSize);
        setControlValue('lineHeight', settings.lineHeight);
        setControlValue('paragraphGap', settings.paragraphGap);
        setControlValue('contWidth', settings.contWidth);

        root.style.setProperty('--font-size-baze', `${settings.fontSize}px`);
        root.style.setProperty('--block-padding', `${settings.paragraphGap / 2}px`);
        root.style.setProperty('--text-line-height', String(settings.lineHeight));
        root.style.setProperty('--text-intend', settings.indent ? '0.875em' : '0');
        root.style.setProperty('--cont-width', `${settings.contWidth}%`);
        root.style.setProperty('--navigation-display', settings.navigation ? 'flex' : 'none');
        root.style.setProperty('--font-family', fontMap[settings.fontFamily] || fontMap['Vag Rounded Next']);
        root.style.setProperty('--primary-text-color', themeMap[settings.theme]?.text ?? '#e9e9e9');
        root.style.setProperty('--body-bg-color', themeMap[settings.theme]?.bg ?? 'rgb(7, 18, 32)');

        if (chapterTitle) {
            chapterTitle.style.display = settings.title ? '' : 'none';
        }

        chapterContent?.querySelectorAll('img').forEach((image) => {
            image.style.display = settings.images ? '' : 'none';
        });
    }

    let settings = loadSettings();

    function updateSetting(name, value) {
        settings = { ...settings, [name]: value };
        saveSettings(settings);
        applySettings(settings);
    }

    function setSettingsPanelOpen(isOpen) {
        if (!readSettings) {
            return;
        }

        readSettings.classList.toggle('is-open', isOpen);
        readSettings.classList.toggle('not-open', !isOpen);
    }

    function bindCheckbox(name) {
        controls[name]?.addEventListener('change', (event) => {
            updateSetting(name, event.target.checked);
        });
    }

    function bindRange(name, formatValue = String) {
        controls[name]?.addEventListener('input', (event) => {
            const value = Number(event.target.value);

            if (Number.isNaN(value)) {
                return;
            }

            if (valueOutputs[name]) {
                valueOutputs[name].textContent = formatValue(value);
            }

            updateSetting(name, value);
        });
    }

    function setupChoiceGroup(selector, settingName) {
        const options = Array.from(document.querySelectorAll(selector));

        options.forEach((option) => {
            const optionValue = option.textContent.trim();
            option.classList.toggle('selected', optionValue === settings[settingName]);

            option.addEventListener('click', () => {
                updateSetting(settingName, optionValue);
                options.forEach((item) => item.classList.toggle('selected', item === option));
            });
        });
    }

    function setupSettingsControls() {
        document.querySelectorAll('.settingsReadBtn').forEach((button) => {
            button.addEventListener('click', () => {
                setSettingsPanelOpen(!readSettings?.classList.contains('is-open'));
            });
        });

        ['indent', 'images', 'title', 'navigation'].forEach(bindCheckbox);
        bindRange('fontSize');
        bindRange('lineHeight', (value) => value.toFixed(1));
        bindRange('paragraphGap');
        bindRange('contWidth');

        setupChoiceGroup('#fontDdContent .dropdown-list-value', 'fontFamily');
        setupChoiceGroup('#themeDdContent .dropdown-list-value', 'theme');
    }

    function setupNavigationVisibility() {
        if (!document.querySelector('.read-nav')) {
            return;
        }

        let navigationIsVisible = true;

        try {
            navigationIsVisible = !JSON.parse(window.localStorage.getItem(NAV_STATE_KEY));
        } catch {
            // Если сохранённое значение недоступно, навигация по умолчанию остаётся видимой.
        }

        let lastScrollY = window.scrollY;

        function setNavigationVisible(isVisible) {
            navigationIsVisible = isVisible;
            root.classList.toggle('nav-hidden', !isVisible);

            try {
                window.localStorage.setItem(NAV_STATE_KEY, JSON.stringify(!isVisible));
            } catch {
                // Навигация продолжает работать и без сохранения состояния.
            }
        }

        setNavigationVisible(navigationIsVisible);

        window.addEventListener('scroll', () => {
            const scrollDelta = window.scrollY - lastScrollY;
            const settingsAreOpen = readSettings?.classList.contains('is-open');

            if (scrollDelta > 5 && navigationIsVisible && !settingsAreOpen) {
                setNavigationVisible(false);
            }

            lastScrollY = window.scrollY;
        }, { passive: true });

        document.addEventListener('click', (event) => {
            if (!chapterContent?.contains(event.target)) {
                return;
            }

            if (navigationIsVisible && readSettings?.classList.contains('is-open')) {
                setSettingsPanelOpen(false);
            }

            setNavigationVisible(!navigationIsVisible);
        });
    }

    function setupReadingProgress() {
        if (!chapterContainer) {
            return;
        }

        let saveTimeout;

        function restoreProgress() {
            try {
                const savedProgress = window.localStorage.getItem(PROGRESS_KEY);

                if (!savedProgress) {
                    return;
                }

                const progress = JSON.parse(savedProgress);
                const scrollY = Number.parseInt(progress?.scrollY, 10);

                if (
                    progress?.pathname === window.location.pathname
                    && Number.isFinite(scrollY)
                    && scrollY > 0
                ) {
                    window.scrollTo(0, scrollY);
                }
            } catch {
                // Повреждённые или недоступные данные не должны мешать чтению главы.
            }
        }

        function saveProgress() {
            try {
                window.localStorage.setItem(PROGRESS_KEY, JSON.stringify({
                    pathname: window.location.pathname,
                    scrollY: window.scrollY,
                }));
            } catch {
                // Сохранение позиции чтения не является обязательным для работы страницы.
            }
        }

        restoreProgress();
        window.setTimeout(restoreProgress, 300);
        window.addEventListener('load', restoreProgress);
        window.addEventListener('scroll', () => {
            window.clearTimeout(saveTimeout);
            saveTimeout = window.setTimeout(saveProgress, 300);
        }, { passive: true });
    }

    applySettings(settings);
    setupSettingsControls();
    setupNavigationVisibility();
    setupReadingProgress();
});
