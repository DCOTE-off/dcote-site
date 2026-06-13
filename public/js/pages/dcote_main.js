const spoilersBtn = document.querySelector('.spoilers-btn');
const ratingDefault = document.querySelector('.rating:not(.spoilers)')
const ratingSpoilers = document.querySelector('.rating.spoilers')

if (spoilersBtn && ratingDefault && ratingSpoilers) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const topClasses = ratingDefault.closest('.top-classes');
    let isSpoilersVisible = false;
    let isRatingAnimating = false;

    const readRatingState = (rating) => Array.from(rating.querySelectorAll('.school-class'), (card) => {
        const image = card.querySelector('img');
        const points = card.querySelector('.points-bg');

        return {
            key: card.dataset.ratingKey,
            className: card.querySelector('.text h3').textContent,
            leader: card.querySelector('.text p').textContent,
            points: card.querySelector('.points-bg b').textContent,
            imageSrc: image.getAttribute('src'),
            imageAlt: image.getAttribute('alt') || '',
            pointsWidth: points.style.getPropertyValue('--points-width'),
            color: points.style.background,
        };
    });

    const ratingStates = {
        default: readRatingState(ratingDefault),
        spoilers: readRatingState(ratingSpoilers),
    };

    ratingStates.spoilers.forEach(({ imageSrc }) => {
        const image = new Image();
        image.src = imageSrc;
    });

    ratingSpoilers.remove();

    const updateCard = (card, state, index) => {
        const image = card.querySelector('img:not(.rating-image-outgoing)');
        const points = card.querySelector('.points-bg');
        const oldImageSrc = image.getAttribute('src');

        card.dataset.ratingKey = state.key;
        card.querySelector('.text h3').textContent = state.className;
        card.querySelector('.text p').textContent = state.leader;
        card.querySelector('.points-bg b').textContent = state.points;
        points.style.setProperty('--points-width', state.pointsWidth);
        points.style.setProperty('--rating-index', index);
        points.style.background = state.color;

        if (oldImageSrc === state.imageSrc) {
            return;
        }

        if (reducedMotion.matches) {
            image.setAttribute('src', state.imageSrc);
            image.setAttribute('alt', state.imageAlt);
            return;
        }

        const oldImage = image.cloneNode();
        oldImage.classList.add('rating-image-outgoing');
        oldImage.removeAttribute('loading');
        card.prepend(oldImage);

        image.classList.add('rating-image-incoming');
        image.setAttribute('src', state.imageSrc);
        image.setAttribute('alt', state.imageAlt);

        window.setTimeout(() => {
            oldImage.remove();
            image.classList.remove('rating-image-incoming');
        }, 550);
    };

    const showRatingState = (stateName) => {
        if (isRatingAnimating) {
            return;
        }

        const targetState = ratingStates[stateName];
        const cards = Array.from(ratingDefault.querySelectorAll('.school-class'));
        const firstRects = new Map(cards.map((card) => [card, card.getBoundingClientRect()]));
        const firstPointsWidths = new Map(cards.map((card) => [
            card,
            card.querySelector('.points-bg').getBoundingClientRect().width,
        ]));
        const cardsByKey = new Map(cards.map((card) => [card.dataset.ratingKey, card]));
        const unmatchedCards = cards.filter((card) => !targetState.some(({ key }) => key === card.dataset.ratingKey));
        const orderedCards = targetState.map((state) => cardsByKey.get(state.key) || unmatchedCards.shift());

        ratingDefault.classList.add('rating-intro-complete');

        orderedCards.forEach((card, index) => {
            updateCard(card, targetState[index], index);
            ratingDefault.append(card);
        });

        spoilersBtn.classList.toggle('spoilers-true', stateName === 'spoilers');
        spoilersBtn.setAttribute('aria-pressed', String(stateName === 'spoilers'));

        if (reducedMotion.matches) {
            return;
        }

        isRatingAnimating = true;
        const cardAnimations = orderedCards.map((card) => {
            const firstRect = firstRects.get(card);
            const lastRect = card.getBoundingClientRect();
            const offsetY = firstRect.top - lastRect.top;

            return card.animate([
                { transform: `translate3d(0, ${offsetY}px, 0)` },
                { transform: 'translate3d(0, 0, 0)' },
            ], {
                duration: 650,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
            }).finished.catch(() => {});
        });

        const pointsAnimations = orderedCards.map((card) => {
            const points = card.querySelector('.points-bg');
            const firstWidth = firstPointsWidths.get(card);
            const lastWidth = points.getBoundingClientRect().width;

            return points.animate([
                { width: `${firstWidth}px` },
                { width: `${lastWidth}px` },
            ], {
                duration: 650,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
            }).finished.catch(() => {});
        });

        Promise.all([...cardAnimations, ...pointsAnimations]).then(() => {
            isRatingAnimating = false;
        });
    };

    spoilersBtn.addEventListener('click', () => {
        if (isRatingAnimating) {
            return;
        }

        isSpoilersVisible = !isSpoilersVisible;
        showRatingState(isSpoilersVisible ? 'spoilers' : 'default');
    });

    ratingDefault.classList.add('rating-intro-ready');

    const revealRating = () => {
        ratingDefault.classList.add('is-rating-visible');

        if (!reducedMotion.matches) {
            window.setTimeout(() => {
                ratingDefault.classList.add('rating-intro-complete');
            }, 1200);
        }
    };

    if (reducedMotion.matches || !topClasses || !('IntersectionObserver' in window)) {
        revealRating();
    } else {
        const ratingObserver = new IntersectionObserver(([entry], observer) => {
            if (!entry.isIntersecting) {
                return;
            }

            revealRating();
            observer.disconnect();
        }, { threshold: 0.25 });

        ratingObserver.observe(topClasses);
    }
}

const popularSlides = Array.from(document.querySelectorAll('[data-popular-slide]'));
const popularPrev = document.querySelector('[data-popular-prev]');
const popularNext = document.querySelector('[data-popular-next]');

if (popularSlides.length && popularPrev && popularNext) {
    let activePopularIndex = 0;
    const isPopularCarousel = popularSlides.length >= 3;

    const updatePopularNavigation = () => {
        if (isPopularCarousel) {
            popularPrev.disabled = false;
            popularNext.disabled = false;
            return;
        }

        popularPrev.disabled = activePopularIndex === 0;
        popularNext.disabled = activePopularIndex === popularSlides.length - 1;
    };

    const showPopularSlide = (index) => {
        activePopularIndex = isPopularCarousel
            ? (index + popularSlides.length) % popularSlides.length
            : Math.max(0, Math.min(popularSlides.length - 1, index));

        popularSlides.forEach((slide, slideIndex) => {
            slide.hidden = slideIndex !== activePopularIndex;
        });

        updatePopularNavigation();
    };

    popularPrev.addEventListener('click', () => showPopularSlide(activePopularIndex - 1));
    popularNext.addEventListener('click', () => showPopularSlide(activePopularIndex + 1));

    showPopularSlide(activePopularIndex);
}

const updatesViewport = document.querySelector('.updates-news-viewport');
const updatesNews = updatesViewport?.querySelector('.updates-news');
const updates = updatesViewport?.closest('.updates');
const updatesScrollbar = updatesViewport?.querySelector('.updates-scrollbar');
const updatesScrollbarThumb = updatesScrollbar?.querySelector('.updates-scrollbar-thumb');

if (updates && updatesViewport && updatesNews && updatesScrollbar && updatesScrollbarThumb) {
    const updateUpdatesOverflow = () => {
        const overflowThreshold = 1;
        const firstUpdate = updatesNews.firstElementChild;
        const lastUpdate = updatesNews.lastElementChild;
        const contentHeight = firstUpdate && lastUpdate
            ? lastUpdate.offsetTop + lastUpdate.offsetHeight - firstUpdate.offsetTop
            : 0;
        const isOverflowing = contentHeight > updatesNews.clientHeight + overflowThreshold;
        const isAtEnd = updatesNews.scrollTop + updatesNews.clientHeight >= updatesNews.scrollHeight - overflowThreshold;

        updates.classList.toggle('is-overflowing', isOverflowing);
        updates.classList.toggle('is-at-end', !isOverflowing || isAtEnd);

        const scrollRange = updatesNews.scrollHeight - updatesNews.clientHeight;
        const trackHeight = updatesScrollbar.clientHeight;
        const minThumbHeight = Math.min(trackHeight, 20);
        const thumbHeight = scrollRange > 0
            ? Math.max(minThumbHeight, trackHeight * updatesNews.clientHeight / updatesNews.scrollHeight)
            : trackHeight;
        const thumbTravel = trackHeight - thumbHeight;
        const thumbOffset = scrollRange > 0
            ? updatesNews.scrollTop / scrollRange * thumbTravel
            : 0;

        updatesScrollbarThumb.style.setProperty('--updates-scrollbar-thumb-height', `${thumbHeight}px`);
        updatesScrollbarThumb.style.setProperty('--updates-scrollbar-thumb-offset', `${thumbOffset}px`);
    };

    updatesNews.addEventListener('scroll', updateUpdatesOverflow, { passive: true });

    updatesScrollbar.addEventListener('pointerdown', (event) => {
        if (event.target === updatesScrollbarThumb) {
            return;
        }

        const trackRect = updatesScrollbar.getBoundingClientRect();
        const thumbHeight = updatesScrollbarThumb.offsetHeight;
        const targetOffset = event.clientY - trackRect.top - thumbHeight / 2;
        const thumbTravel = trackRect.height - thumbHeight;
        const scrollRange = updatesNews.scrollHeight - updatesNews.clientHeight;

        updatesNews.scrollTop = thumbTravel > 0
            ? Math.max(0, Math.min(thumbTravel, targetOffset)) / thumbTravel * scrollRange
            : 0;
    });

    updatesScrollbarThumb.addEventListener('pointerdown', (event) => {
        event.preventDefault();

        const startY = event.clientY;
        const startScrollTop = updatesNews.scrollTop;
        const thumbTravel = updatesScrollbar.clientHeight - updatesScrollbarThumb.offsetHeight;
        const scrollRange = updatesNews.scrollHeight - updatesNews.clientHeight;

        updatesScrollbarThumb.setPointerCapture(event.pointerId);

        const moveThumb = (moveEvent) => {
            updatesNews.scrollTop = thumbTravel > 0
                ? startScrollTop + (moveEvent.clientY - startY) / thumbTravel * scrollRange
                : 0;
        };

        const stopDragging = () => {
            updatesScrollbarThumb.removeEventListener('pointermove', moveThumb);
            updatesScrollbarThumb.removeEventListener('pointerup', stopDragging);
            updatesScrollbarThumb.removeEventListener('pointercancel', stopDragging);
        };

        updatesScrollbarThumb.addEventListener('pointermove', moveThumb);
        updatesScrollbarThumb.addEventListener('pointerup', stopDragging);
        updatesScrollbarThumb.addEventListener('pointercancel', stopDragging);
    });

    const updatesResizeObserver = new ResizeObserver(updateUpdatesOverflow);
    updatesResizeObserver.observe(updatesViewport);
    updatesResizeObserver.observe(updatesNews);

    window.addEventListener('load', updateUpdatesOverflow, { once: true });
    requestAnimationFrame(updateUpdatesOverflow);
}
