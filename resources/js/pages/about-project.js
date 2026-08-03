document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.expandable-card').forEach((department) => {
        const summary = department.querySelector('summary');
        const members = department.querySelector('.department-members');
        const scrollbar = department.querySelector('.department-scrollbar');
        const thumb = department.querySelector('.department-scrollbar-thumb');
        let dropdownAnimation = null;
        let isClosing = false;
        let isOpening = false;

        const finishDropdownAnimation = (isOpen) => {
            department.open = isOpen;
            department.classList.remove('is-animating', 'is-closing');
            department.style.height = '';
            dropdownAnimation = null;
            isClosing = false;
            isOpening = false;
        };

        const closeDropdown = () => {
            isClosing = true;
            isOpening = false;
            department.classList.add('is-animating', 'is-closing');

            const startHeight = `${department.offsetHeight}px`;
            const styles = window.getComputedStyle(department);
            const collapsedHeight = summary.offsetHeight
                + parseFloat(styles.paddingTop)
                + parseFloat(styles.paddingBottom);

            dropdownAnimation?.cancel();
            dropdownAnimation = department.animate(
                { height: [startHeight, `${collapsedHeight}px`] },
                { duration: 300, easing: 'ease-in-out' },
            );
            dropdownAnimation.onfinish = () => finishDropdownAnimation(false);
        };

        const openDropdown = () => {
            isOpening = true;
            isClosing = false;
            department.classList.add('is-animating');
            department.classList.remove('is-closing');

            const startHeight = `${department.offsetHeight}px`;
            department.open = true;
            const endHeight = `${department.scrollHeight}px`;

            dropdownAnimation?.cancel();
            dropdownAnimation = department.animate(
                { height: [startHeight, endHeight] },
                { duration: 300, easing: 'ease-in-out' },
            );
            dropdownAnimation.onfinish = () => finishDropdownAnimation(true);
        };

        summary?.addEventListener('click', (event) => {
            event.preventDefault();

            if (!department.animate || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                dropdownAnimation?.cancel();
                finishDropdownAnimation(!department.open);
                return;
            }

            if (isClosing || !department.open) {
                openDropdown();
                return;
            }

            if (isOpening || department.open) {
                closeDropdown();
            }
        });

        if (!members || !scrollbar || !thumb) {
            return;
        }

        const updateScrollbar = () => {
            const maxScroll = members.scrollWidth - members.clientWidth;
            members.classList.toggle('is-scrollable', maxScroll > 0);

            const visibleRatio = maxScroll > 0 ? members.clientWidth / members.scrollWidth : 1;
            const thumbWidth = scrollbar.clientWidth * visibleRatio;
            const maxThumbOffset = scrollbar.clientWidth - thumbWidth;
            const scrollProgress = maxScroll > 0 ? members.scrollLeft / maxScroll : 0;

            thumb.style.width = `${thumbWidth}px`;
            thumb.style.transform = `translateX(${maxThumbOffset * scrollProgress}px)`;
        };

        members.addEventListener('scroll', updateScrollbar, { passive: true });
        scrollbar.addEventListener('click', (event) => {
            if (event.target === thumb) {
                return;
            }

            const bounds = scrollbar.getBoundingClientRect();
            const clickProgress = Math.min(Math.max((event.clientX - bounds.left) / bounds.width, 0), 1);

            members.scrollTo({
                left: (members.scrollWidth - members.clientWidth) * clickProgress,
                behavior: 'smooth',
            });
        });

        thumb.addEventListener('pointerdown', (event) => {
            const maxScroll = members.scrollWidth - members.clientWidth;
            const maxThumbOffset = scrollbar.clientWidth - thumb.clientWidth;

            if (maxScroll <= 0 || maxThumbOffset <= 0) {
                return;
            }

            const startX = event.clientX;
            const startScrollLeft = members.scrollLeft;

            thumb.setPointerCapture(event.pointerId);

            const moveThumb = (moveEvent) => {
                const pointerOffset = moveEvent.clientX - startX;
                members.scrollLeft = startScrollLeft + pointerOffset * (maxScroll / maxThumbOffset);
            };

            const stopDragging = () => {
                thumb.removeEventListener('pointermove', moveThumb);
                thumb.removeEventListener('pointerup', stopDragging);
                thumb.removeEventListener('pointercancel', stopDragging);
            };

            thumb.addEventListener('pointermove', moveThumb);
            thumb.addEventListener('pointerup', stopDragging);
            thumb.addEventListener('pointercancel', stopDragging);
        });

        new ResizeObserver(updateScrollbar).observe(members);
        updateScrollbar();
    });
});
