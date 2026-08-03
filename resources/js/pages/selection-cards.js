(function () {
    const CARD_SELECTOR = '.selection-card';
    const DESKTOP_QUERY = window.matchMedia('(min-width: 769px)');
    const FALLBACK_COVER_RATIO = 1220 / 1706;
    const CARD_WIDTH_SHARE = 0.95;
    const DESCRIPTION_WIDTH_SHARE = 0.55;

    const toNumber = value => {
        const parsed = Number.parseFloat(value);
        return Number.isFinite(parsed) ? parsed : 0;
    };

    const getContentWidth = element => {
        const styles = window.getComputedStyle(element);
        return element.getBoundingClientRect().width
            - toNumber(styles.paddingLeft)
            - toNumber(styles.paddingRight);
    };

    const getCoverRatio = image => {
        if (image.naturalWidth > 0 && image.naturalHeight > 0) {
            return image.naturalWidth / image.naturalHeight;
        }

        return FALLBACK_COVER_RATIO;
    };

    const clearCardSizing = ({ card, imageWrapper, description }) => {
        card.style.removeProperty('width');
        imageWrapper.style.removeProperty('flex-basis');
        imageWrapper.style.removeProperty('width');
        imageWrapper.style.removeProperty('height');
        description.style.removeProperty('flex-basis');
        description.style.removeProperty('width');
    };

    const init = () => {
        const cards = Array.from(document.querySelectorAll(CARD_SELECTOR))
            .map(card => {
                const imageWrapper = card.querySelector('.image-wrapper');
                const image = imageWrapper?.querySelector('img');
                const description = card.querySelector('.desc');

                return imageWrapper && image && description && card.parentElement
                    ? { card, imageWrapper, image, description, parent: card.parentElement }
                    : null;
            })
            .filter(Boolean);

        if (cards.length === 0) {
            return;
        }

        let frame = 0;

        const update = () => {
            frame = 0;

            if (!DESKTOP_QUERY.matches) {
                cards.forEach(clearCardSizing);
                return;
            }

            const cardWidths = new Map();

            // Fix every text column first, then measure heights in one layout pass.
            cards.forEach(({ parent, description }) => {
                if (!cardWidths.has(parent)) {
                    cardWidths.set(parent, getContentWidth(parent) * CARD_WIDTH_SHARE);
                }

                const descriptionWidth = cardWidths.get(parent) * DESCRIPTION_WIDTH_SHARE;
                description.style.flexBasis = `${descriptionWidth}px`;
                description.style.width = `${descriptionWidth}px`;
            });

            cards.forEach(({ card, imageWrapper, image, description }) => {
                const descriptionRect = description.getBoundingClientRect();
                const descriptionWidth = descriptionRect.width;
                const descriptionHeight = descriptionRect.height;
                const coverWidth = descriptionHeight * getCoverRatio(image);

                card.style.width = `${coverWidth + descriptionWidth}px`;
                imageWrapper.style.flexBasis = `${coverWidth}px`;
                imageWrapper.style.width = `${coverWidth}px`;
                imageWrapper.style.height = `${descriptionHeight}px`;
            });
        };

        const schedule = () => {
            if (!frame) {
                frame = window.requestAnimationFrame(update);
            }
        };

        cards.forEach(({ image }) => {
            if (!image.complete) {
                image.addEventListener('load', schedule, { once: true });
            }
        });

        window.addEventListener('resize', schedule, { passive: true });
        DESKTOP_QUERY.addEventListener('change', schedule);
        document.fonts?.ready.then(schedule);

        if ('ResizeObserver' in window) {
            const observer = new ResizeObserver(schedule);
            const targets = new Set();

            cards.forEach(({ parent, description }) => {
                targets.add(parent);
                targets.add(description);
            });
            targets.forEach(target => observer.observe(target));
        }

        schedule();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
