document.addEventListener('DOMContentLoaded', () => {
    const carouselRoot = document.querySelector('.grid-images');

    if (!carouselRoot || typeof window.EmblaCarousel !== 'function') {
        return;
    }

    const mobileQuery = window.matchMedia('(max-width: 768px)');
    const links = Array.from(carouselRoot.querySelectorAll(':scope > a'));
    const originalParent = carouselRoot;
    let container = null;
    let carousel = null;
    let initializing = false;
    let initializationId = 0;

    function setSelectedSlide() {
        const selectedIndex = carousel.selectedScrollSnap();

        links.forEach((link, index) => {
            link.classList.toggle('is-selected', index === selectedIndex);
        });
    }

    function waitForImages() {
        const images = Array.from(carouselRoot.querySelectorAll('img'));

        return Promise.all(images.map((image) => {
            if (image.complete) {
                return Promise.resolve();
            }

            return new Promise((resolve) => {
                const finish = () => {
                    window.clearTimeout(timeout);
                    resolve();
                };
                const timeout = window.setTimeout(finish, 3000);

                image.addEventListener('load', finish, { once: true });
                image.addEventListener('error', finish, { once: true });
            });
        }));
    }

    async function enableCarousel() {
        if (carousel || initializing || links.length === 0 || !mobileQuery.matches) {
            return;
        }

        initializing = true;
        const currentInitializationId = ++initializationId;
        await waitForImages();

        if (
            carousel
            || !mobileQuery.matches
            || currentInitializationId !== initializationId
        ) {
            initializing = false;
            return;
        }

        container = document.createElement('div');
        container.className = 'grid-images__container';

        links.forEach((link, index) => {
            link.dataset.carouselIndex = String(index);
            container.append(link);

            link.addEventListener('click', handleLinkClick);
        });

        carouselRoot.append(container);
        carousel = window.EmblaCarousel(carouselRoot, {
            align: 'center',
            containScroll: false,
            dragFree: false,
            loop: links.length > 1,
            slidesToScroll: 1,
            skipSnaps: false,
        });

        carousel.on('select', setSelectedSlide);
        carousel.on('reInit', setSelectedSlide);
        setSelectedSlide();
        carouselRoot.classList.add('is-carousel-ready');
        initializing = false;
    }

    function disableCarousel() {
        initializationId++;

        if (!carousel) {
            initializing = false;
            return;
        }

        carousel.destroy();
        links.forEach((link) => {
            link.classList.remove('is-selected');
            link.removeEventListener('click', handleLinkClick);
            delete link.dataset.carouselIndex;
            originalParent.append(link);
        });

        container?.remove();
        carouselRoot.classList.remove('is-carousel-ready');
        carousel = null;
        container = null;
        initializing = false;
    }

    function handleLinkClick(event) {
        const link = event.currentTarget;
        const selectedLink = links[carousel.selectedScrollSnap()];

        if (link === selectedLink) {
            return;
        }

        event.preventDefault();

        const rootCenter = carouselRoot.getBoundingClientRect().left + carouselRoot.offsetWidth / 2;
        const linkCenter = link.getBoundingClientRect().left + link.offsetWidth / 2;

        if (linkCenter > rootCenter) {
            carousel.scrollNext();
            return;
        }

        carousel.scrollPrev();
    }

    function syncMode(event) {
        if (event.matches) {
            enableCarousel();
        } else {
            disableCarousel();
        }
    }

    syncMode(mobileQuery);
    mobileQuery.addEventListener('change', syncMode);
});

