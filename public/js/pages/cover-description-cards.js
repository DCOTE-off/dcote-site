document.addEventListener('DOMContentLoaded', () => {
    const desktopMedia = window.matchMedia('(min-width: 769px)');
    const cards = Array.from(document.querySelectorAll('[data-cover-description-card]'))
        .map((card) => ({
            card,
            image: card.querySelector(':scope > .image-wrapper img'),
            description: card.querySelector(':scope > .desc'),
        }))
        .filter(({ image, description }) => image && description);

    if (!cards.length) {
        return;
    }

    let animationFrame = null;

    const syncCard = ({ card, image, description }) => {
        if (!desktopMedia.matches) {
            card.style.removeProperty('--cover-width');
            card.style.removeProperty('--cover-aspect-ratio');
            return;
        }

        if (!image.naturalWidth || !image.naturalHeight) {
            return;
        }

        const aspectRatio = image.naturalWidth / image.naturalHeight;
        const coverWidth = description.offsetHeight * aspectRatio;

        card.style.setProperty(
            '--cover-aspect-ratio',
            `${image.naturalWidth} / ${image.naturalHeight}`
        );
        card.style.setProperty('--cover-width', `${coverWidth}px`);
    };

    const scheduleSync = () => {
        if (animationFrame !== null) {
            cancelAnimationFrame(animationFrame);
        }

        animationFrame = requestAnimationFrame(() => {
            cards.forEach(syncCard);
            animationFrame = null;
        });
    };

    const descriptionObserver = new ResizeObserver(scheduleSync);

    cards.forEach(({ image, description }) => {
        descriptionObserver.observe(description);
        image.addEventListener('load', scheduleSync);
    });

    desktopMedia.addEventListener('change', scheduleSync);
    window.addEventListener('resize', scheduleSync);
    document.fonts?.ready.then(scheduleSync);
    scheduleSync();
});
