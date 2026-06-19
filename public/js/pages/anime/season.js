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
});
