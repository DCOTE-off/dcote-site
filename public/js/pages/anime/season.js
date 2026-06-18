document.addEventListener('DOMContentLoaded', () => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const sortingContainers = new WeakSet();

    function getSortContainer(button) {
        const section = button.closest('.cont2, .chapters-cont');
        return section?.querySelector('.grid-area') || document.querySelector('.grid-area');
    }

    function updateSortIcons(button, isDescending) {
        const sortAsc = button.querySelector('.sort-ascending');
        const sortDesc = button.querySelector('.sort-descending');

        if (!sortAsc || !sortDesc) {
            return;
        }

        sortAsc.style.display = isDescending ? 'none' : 'block';
        sortDesc.style.display = isDescending ? 'block' : 'none';
    }

    function sortGridWithAnimation(container, button) {
        const items = Array.from(container.children);

        if (items.length < 2 || sortingContainers.has(container)) {
            return;
        }

        const isDescending = container.dataset.sortDirection === 'descending'
            || container.style.flexDirection === 'column-reverse';
        const shouldAnimate = !reducedMotion.matches && typeof items[0].animate === 'function';
        const firstRects = shouldAnimate
            ? new Map(items.map((item) => [item, item.getBoundingClientRect()]))
            : null;
        const orderedItems = [...items].reverse();

        container.style.flexDirection = 'column';
        orderedItems.forEach((item) => container.append(item));
        container.dataset.sortDirection = isDescending ? 'ascending' : 'descending';
        updateSortIcons(button, !isDescending);
        button.setAttribute('aria-pressed', String(!isDescending));

        if (!shouldAnimate) {
            return;
        }

        sortingContainers.add(container);
        container.classList.add('is-sorting');

        const animations = orderedItems.map((item) => {
            const firstRect = firstRects.get(item);
            const lastRect = item.getBoundingClientRect();
            const offsetX = firstRect.left - lastRect.left;
            const offsetY = firstRect.top - lastRect.top;

            if (Math.abs(offsetX) < 0.5 && Math.abs(offsetY) < 0.5) {
                return Promise.resolve();
            }

            item.style.willChange = 'transform';

            return item.animate([
                { transform: `translate3d(${offsetX}px, ${offsetY}px, 0)` },
                { transform: 'translate3d(0, 0, 0)' },
            ], {
                duration: 520,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
            }).finished.finally(() => {
                item.style.willChange = '';
            });
        });

        Promise.all(animations.map((animation) => animation.catch(() => {}))).then(() => {
            sortingContainers.delete(container);
            container.classList.remove('is-sorting');
        });
    }

    document.querySelectorAll('.sort-toggle').forEach((button) => {
        button.addEventListener('click', () => {
            const container = getSortContainer(button);

            if (!container) {
                return;
            }

            sortGridWithAnimation(container, button);
        });
    });

    const ratingPopups = document.querySelectorAll('.rating');
    ratingPopups.forEach(rating => {
        const popupWindow = rating.querySelector('.popup-stars');
        const ratingBtnForPopup = rating.querySelector('.rating-btn-for-popup');
        const stars = rating.querySelectorAll('.rating-btn');
        let selectedRating = 0;

        ratingBtnForPopup.addEventListener('click', (e) => {
            e.stopPropagation();
            popupWindow.classList.toggle('hidden');
            ratingBtnForPopup.classList.toggle('active');
        });

        function highlightStars(upTo) {
            stars.forEach(star => {
                const starRating = +star.dataset.rating;
                star.classList.toggle('active', starRating <= upTo);
            });
        }

        stars.forEach(star => {
            star.addEventListener('mouseenter', () => highlightStars(+star.dataset.rating));
            star.addEventListener('mouseleave', () => highlightStars(selectedRating));
            star.addEventListener('click', () => {
                const clickedRating = +star.dataset.rating;
                if (clickedRating === selectedRating) {
                    selectedRating = 0;
                    highlightStars(0);
                    ratingBtnForPopup.classList.remove('active_long');
                } else {
                    selectedRating = clickedRating;
                    highlightStars(selectedRating);
                    ratingBtnForPopup.classList.add('active_long');
                }
            });
        });
    });
    document.addEventListener('click', (e) => {
        ratingPopups.forEach(rating => {
            const popup = rating.querySelector('.popup-stars');
            const btn = rating.querySelector('.rating-btn-for-popup');
            if (!popup.classList.contains('hidden')) {
                const isInside = popup.contains(e.target);
                const isOnTrigger = e.target.closest('.rating-btn-for-popup');
                if (!isInside && !isOnTrigger) {
                    popup.classList.add('hidden');
                    btn.classList.remove('active');
                }
            }
        });
    });
});
