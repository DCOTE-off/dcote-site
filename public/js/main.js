document.addEventListener('DOMContentLoaded', () => {
    
    const fixBackgroundHeight = () => {
    // Вычисляем реальную высоту один раз
    const fullHeight = window.innerHeight;
    document.documentElement.style.setProperty('--fixed-height', `${fullHeight}px`);
    };

    const toast = document.getElementById('toast-success');

    if (toast) {
        // Показываем с небольшой задержкой для эффекта
        setTimeout(() => {
            toast.classList.add('is-open');
        }, 100);

        setTimeout(() => {
            toast.classList.remove('is-open');
            toast.classList.add('not-open');
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

                // Логика скрытия навбара
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
});