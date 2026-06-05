document.addEventListener('DOMContentLoaded', () => {
    
    const fixBackgroundHeight = () => {
    const fullHeight = window.innerHeight;
    document.documentElement.style.setProperty('--fixed-height', `${fullHeight}px`);
    };

    const toastSuccess = document.getElementById('toast-success');
    const toastError = document.getElementById('toast-error');

    if (toastSuccess) {
        setTimeout(() => {
            toastSuccess.classList.add('is-open');
        }, 100);
        setTimeout(() => {
            toastSuccess.classList.remove('is-open');
            toastSuccess.classList.add('not-open');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }
    if (toastError) {
        setTimeout(() => {
            toastError.classList.add('is-open');
        }, 100);
        setTimeout(() => {
            toastError.classList.remove('is-open');
            toastError.classList.add('not-open');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }

    fixBackgroundHeight();
    window.addEventListener('orientationchange', () => {
        setTimeout(fixBackgroundHeight, 100); 
    });

    const hamburger = document.getElementById('hamburgerBtn');
    const sideMenu = document.getElementById('sideMenu');
    const closeBtn = document.querySelector('.closeMenu');

    if (hamburger && sideMenu) {
        hamburger.addEventListener('click', () => {
            if (sideMenu.classList.contains('is-open')) {
                sideMenu.classList.remove('is-open');
                sideMenu.classList.add('not-open');
            } else {
                sideMenu.classList.remove('not-open');
                sideMenu.classList.add('is-open');
            }
        });

        document.addEventListener('click', (event) => {
            const isClickOnHamburger = hamburger.contains(event.target);
            const isClickInsideMenu = sideMenu.contains(event.target);

            if (!isClickOnHamburger && !isClickInsideMenu) {
                if (sideMenu.classList.contains('is-open')) {
                    sideMenu.classList.remove('is-open');
                    sideMenu.classList.add('not-open');
                }
            }
        });
    }
    const nav = document.querySelector('.mobile-bottom-nav');

    if (nav) {
        const updateNavHeight = () => {
            const height = nav.offsetHeight;
            document.documentElement.style.setProperty('--nav-height', `${height}px`);
        };

        const navObserver = new ResizeObserver(updateNavHeight);
        navObserver.observe(nav);

        if (window.visualViewport) {
            const initialHeight = window.visualViewport.height;
            
            window.visualViewport.addEventListener('resize', () => {
                const currentHeight = window.visualViewport.height;
                const isKeyboardOpen = currentHeight < initialHeight * 0.85;

                if (isKeyboardOpen) {
                    nav.style.opacity = '0';
                    nav.style.pointerEvents = 'none';
                } else {
                    nav.style.opacity = '1';
                    nav.style.pointerEvents = 'auto';
                }
            });
        }
        
        updateNavHeight();
    }

    const accountDdDesktopBtn = document.getElementById('account-dropdown-desktop-btn');
    const accountMenuDesktop = document.querySelector('.account-dropdown-desktop')

    if (accountDdDesktopBtn) {
        accountDdDesktopBtn.addEventListener('click', () => {
            if (accountMenuDesktop.classList.contains('is-open')) {
                accountMenuDesktop.classList.remove('is-open');
                accountMenuDesktop.classList.add('not-open');
            } else {
                accountMenuDesktop.classList.remove('not-open');
                accountMenuDesktop.classList.add('is-open');
            }
        });

        document.addEventListener('click', (event) => {
            const isClickOnDdDesktopAccountBtn = accountDdDesktopBtn.contains(event.target);
            const isClickInsideMenu = accountMenuDesktop.contains(event.target);

            if (!isClickOnDdDesktopAccountBtn && !isClickInsideMenu) {
                if (accountMenuDesktop.classList.contains('is-open')) {
                    accountMenuDesktop.classList.remove('is-open');
                    accountMenuDesktop.classList.add('not-open');
                }
            }
        });
    }

    const header = document.querySelector('.navbar');

    if (header) {
        const updateHeaderHeight = () => {
            const headerHeight = header.offsetHeight;
            document.documentElement.style.setProperty('--header-height', `${headerHeight}px`);
        };
        updateHeaderHeight();
    }
});