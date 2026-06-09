document.addEventListener('DOMContentLoaded', () => {
    const storageKey = 'dcote-reading-settings';
    const settingsReadBtn = document.getElementById('settingsReadBtn');
    const readSettings = document.querySelector('.read-settings');
    const chapterContainer = document.querySelector('.chapter-container');
    const chapterContent = document.querySelector('.chapter-content');
    const chapterTitle = document.querySelector('.main-title');
    const root = document.documentElement;

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

    const values = {
        fontSize: document.getElementById('fontSizeValue'),
        lineHeight: document.getElementById('lineHeightValue'),
        paragraphGap: document.getElementById('paragraphGapValue'),
        contWidth: document.getElementById('contWidthValue'),
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
    };

    function loadSettings() {
        try {
            const saved = window.localStorage.getItem(storageKey);
            return saved ? JSON.parse(saved) : defaultSettings;
        } catch (error) {
            return defaultSettings;
        }
    }

    function saveSettings(settings) {
        try {
            window.localStorage.setItem(storageKey, JSON.stringify(settings));
        } catch (error) {
        }
    }

    function applySettings(settings) {
        if (controls.indent) controls.indent.checked = settings.indent;
        if (controls.images) controls.images.checked = settings.images;
        if (controls.title) controls.title.checked = settings.title;
        if (controls.fontSize) controls.fontSize.value = settings.fontSize;
        if (controls.lineHeight) controls.lineHeight.value = settings.lineHeight;
        if (controls.paragraphGap) controls.paragraphGap.value = settings.paragraphGap;
        if (controls.contWidth) controls.contWidth.value = settings.contWidth;

        if (values.fontSize) values.fontSize.textContent = settings.fontSize;
        if (values.lineHeight) values.lineHeight.textContent = settings.lineHeight;
        if (values.paragraphGap) values.paragraphGap.textContent = settings.paragraphGap;
        if (values.contWidth) values.contWidth.textContent = settings.contWidth;

        root.style.setProperty('--font-size-baze', `${settings.fontSize}px`);
        root.style.setProperty('--block-padding', `${(settings.paragraphGap /2)}px`);
        root.style.setProperty('--text-line-height', `${settings.lineHeight}`);
        root.style.setProperty('--text-intend', settings.indent ? '0.875em' : '0');

        root.style.setProperty('--cont-width', `${settings.contWidth}%`);
        root.style.setProperty('--navigation-display', settings.navigation ? 'flex' : 'none');
        if (chapterTitle) chapterTitle.style.display = settings.title ? '' : 'none';

        if (chapterContent) {
            chapterContent.querySelectorAll('img').forEach((img) => {
                img.style.display = settings.images ? '' : 'none';
            });
        }
    }

    function updateSetting(name, value) {
        const currentSettings = loadSettings();
        currentSettings[name] = value;
        saveSettings(currentSettings);
        applySettings(currentSettings);
    }

    if (settingsReadBtn && readSettings) {
        settingsReadBtn.addEventListener('click', () => {
            if (readSettings.classList.contains('is-open')) {
                readSettings.classList.remove('is-open');
                readSettings.classList.add('not-open');
            } else {
                readSettings.classList.remove('not-open');
                readSettings.classList.add('is-open');
            }
        });
    }

    if (controls.indent) {
        controls.indent.addEventListener('change', (event) => {
            updateSetting('indent', event.target.checked);
        });
    }

    if (controls.images) {
        controls.images.addEventListener('change', (event) => {
            updateSetting('images', event.target.checked);
        });
    }

    if (controls.title) {
        controls.title.addEventListener('change', (event) => {
            updateSetting('title', event.target.checked);
        });
    }

    if (controls.navigation) {
        controls.navigation.addEventListener('change', (event) => {
            updateSetting('navigation', event.target.checked);
        });
    }

    if (controls.fontSize) {
        controls.fontSize.addEventListener('input', (event) => {
            const value = Number(event.target.value);
            if (!Number.isNaN(value)) {
                values.fontSize.textContent = value;
                updateSetting('fontSize', value);
            }
        });
    }

    if (controls.lineHeight) {
        controls.lineHeight.addEventListener('input', (event) => {
            const value = Number(event.target.value);
            if (!Number.isNaN(value)) {
                values.lineHeight.textContent = value.toFixed(1);
                updateSetting('lineHeight', value);
            }
        });
    }

    if (controls.paragraphGap) {
        controls.paragraphGap.addEventListener('input', (event) => {
            const value = Number(event.target.value);
            if (!Number.isNaN(value)) {
                values.paragraphGap.textContent = value;
                updateSetting('paragraphGap', value);
            }
        });
    }

    if (controls.contWidth) {
        controls.contWidth.addEventListener('input', (event) => {
            const value = Number(event.target.value);
            if (!Number.isNaN(value)) {
                values.contWidth.textContent = value;
                updateSetting('contWidth', value);
            }
        });
    }

    const settings = loadSettings();
    applySettings(settings);

    // Nav visibility — hide on scroll, show on tap of chapter content
    const readNavEl = document.querySelector('.read-nav');
    const mobileNavEl = document.querySelector('.mobile-bottom-nav');

    if (readNavEl) {
        let navsVisible = true;
        let lastScrollY = window.scrollY;

        function setNavs(visible) {
            navsVisible = visible;
            readNavEl.classList.toggle('is-hidden', !visible);
            if (mobileNavEl) {
                mobileNavEl.classList.toggle('is-hidden', !visible);
            }
        }

        window.addEventListener('scroll', () => {
            const delta = window.scrollY - lastScrollY;
            if (delta > 5 && navsVisible) {
                if (navsVisible && readSettings.classList.contains('is-open')) {
                    readSettings.classList.remove('is-open');
                    readSettings.classList.add('not-open');
                }
                setNavs(false);
            }
            lastScrollY = window.scrollY;
        }, { passive: true });

        document.addEventListener('click', (event) => {
            if (chapterContent && chapterContent.contains(event.target)) {
                if (navsVisible && readSettings.classList.contains('is-open')) {
                    readSettings.classList.remove('is-open');
                    readSettings.classList.add('not-open');
                }
                setNavs(!navsVisible);
            }
        });
    }

    // Reading progress — remember scroll only for the last visited page
    if (chapterContainer) {
        const progressKey = 'dcote-reading-progress';
        let saveTimeout;

        function restoreProgress() {
            try {
                const saved = window.localStorage.getItem(progressKey);
                if (saved) {
                    const data = JSON.parse(saved);
                    if (data && data.pathname === window.location.pathname) {
                        const pos = parseInt(data.scrollY, 10);
                        if (!isNaN(pos) && pos > 0) {
                            window.scrollTo(0, pos);
                        }
                    }
                }
            } catch (e) {}
        }

        // Initial restore after settings are applied
        restoreProgress();
        // Retry after images/layout settle
        setTimeout(restoreProgress, 300);
        window.addEventListener('load', restoreProgress);

        window.addEventListener('scroll', () => {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                try {
                    window.localStorage.setItem(progressKey, JSON.stringify({
                        pathname: window.location.pathname,
                        scrollY: window.scrollY,
                    }));
                } catch (e) {}
            }, 300);
        }, { passive: true });
    }
});