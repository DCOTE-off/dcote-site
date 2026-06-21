document.addEventListener('DOMContentLoaded', () => {
    function setAnimatedOpenState(element, isOpen) {
        element.classList.toggle('is-open', isOpen);
        element.classList.toggle('not-open', !isOpen);
    }

    function setupViewportHeight() {
        const updateHeight = () => {
            document.documentElement.style.setProperty('--fixed-height', `${window.innerHeight}px`);
        };

        updateHeight();
        window.addEventListener('orientationchange', () => {
            window.setTimeout(updateHeight, 100);
        });
    }

    function setupToast(toast, afterRemove) {
        if (!toast) {
            return;
        }

        window.setTimeout(() => toast.classList.add('is-open'), 100);
        window.setTimeout(() => {
            setAnimatedOpenState(toast, false);
            window.setTimeout(() => {
                toast.remove();
                afterRemove?.();
            }, 500);
        }, 4000);
    }

    function setupSideMenu() {
        const hamburger = document.getElementById('hamburgerBtn');
        const sideMenu = document.getElementById('sideMenu');

        if (!hamburger || !sideMenu) {
            return;
        }

        hamburger.addEventListener('click', () => {
            setAnimatedOpenState(sideMenu, !sideMenu.classList.contains('is-open'));
        });

        document.addEventListener('click', (event) => {
            const clickedMenuControl = hamburger.contains(event.target) || sideMenu.contains(event.target);

            if (!clickedMenuControl && sideMenu.classList.contains('is-open')) {
                setAnimatedOpenState(sideMenu, false);
            }
        });
    }

    function setupMobileNavigation() {
        const navigation = document.querySelector('.mobile-bottom-nav');

        if (!navigation || !window.visualViewport) {
            return;
        }

        const initialViewportHeight = window.visualViewport.height;

        window.visualViewport.addEventListener('resize', () => {
            const keyboardIsOpen = window.visualViewport.height < initialViewportHeight * 0.85;

            navigation.style.opacity = keyboardIsOpen ? '0' : '1';
            navigation.style.pointerEvents = keyboardIsOpen ? 'none' : 'auto';
        });
    }

    function setupAccountMenu() {
        const buttons = Array.from(document.querySelectorAll('.account-dropdown-btn'));
        const menu = document.querySelector('.account-dropdown-any');

        if (buttons.length === 0 || !menu) {
            return;
        }

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                setAnimatedOpenState(menu, !menu.classList.contains('is-open'));
            });
        });

        document.addEventListener('click', (event) => {
            const clickedButton = buttons.some((button) => button.contains(event.target));

            if (!clickedButton && !menu.contains(event.target) && menu.classList.contains('is-open')) {
                setAnimatedOpenState(menu, false);
            }
        });
    }

    function setupHeaderHeight() {
        const header = document.querySelector('.navbar');

        if (header) {
            document.documentElement.style.setProperty('--header-height', `${header.offsetHeight}px`);
        }
    }

    const successToast = document.getElementById('toast-success');
    const errorToast = document.getElementById('toast-error');

    setupViewportHeight();
    setupToast(successToast, () => errorToast?.remove());
    setupToast(errorToast);
    setupSideMenu();
    setupMobileNavigation();
    setupAccountMenu();
    setupHeaderHeight();
});
