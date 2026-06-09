const spoilersBtn = document.querySelector('.spoilers-btn');
const ratingDefault = document.querySelector('.rating:not(.spoilers)')
const ratingSpoilers = document.querySelector('.rating.spoilers')
spoilersBtn?.addEventListener('click', () =>{
        ratingDefault.classList.toggle('hidden')
        ratingSpoilers.classList.toggle('hidden')
        spoilersBtn.classList.toggle('spoilers-true')
});

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
