document.addEventListener('DOMContentLoaded', () => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const sortingContainers = new WeakSet();
    const WATCH_STORAGE_PREFIX = 'dcote:anime:watch-state:v1';
    const BOOKMARK_STORAGE_PREFIX = 'dcote:anime:bookmark-state:v1';
    const INTERACTION_DURATION = 350;

    function bindActivation(controls, handler) {
        const elements = controls instanceof Element ? [controls] : controls;

        elements.forEach(control => {
            control.addEventListener('click', handler);
            control.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    handler(event);
                }
            });
        });
    }

    function readStoredState(storageKey, activeState, fallbackState) {
        try {
            return localStorage.getItem(storageKey) === activeState ? activeState : fallbackState;
        } catch {
            return fallbackState;
        }
    }

    function persistState(storageKey, state) {
        try {
            localStorage.setItem(storageKey, state);
        } catch {
            // Состояние серии продолжает работать в рамках страницы, даже если хранилище недоступно.
        }
    }

    function pulseInteraction(element, className) {
        element?.classList.add(className);
        window.setTimeout(() => element?.classList.remove(className), INTERACTION_DURATION);
    }

    function setupDisabledEpisodeControls(container) {
        const selector = [
            '.episode-watch-state[aria-disabled="true"]',
            '.episode-bookmark-toggle[aria-disabled="true"]',
        ].join(', ');

        bindActivation(container.querySelectorAll(selector), event => {
            event.preventDefault();
            event.stopPropagation();
        });
    }

    // Контракт таймера: Blade передаёт ISO-даты будущих серий и текущее время сервера.
    function setupEpisodeCountdowns(container) {
        const serverNow = Date.parse(container.dataset.serverNow);
        const serverClockOffset = Number.isNaN(serverNow) ? 0 : serverNow - Date.now();
        const unitFormatters = {
            day: new Intl.NumberFormat('ru', { style: 'unit', unit: 'day', unitDisplay: 'long' }),
            hour: new Intl.NumberFormat('ru', { style: 'unit', unit: 'hour', unitDisplay: 'long' }),
            minute: new Intl.NumberFormat('ru', { style: 'unit', unit: 'minute', unitDisplay: 'long' }),
        };
        const episodes = getEpisodeItems(container)
            .map(element => ({
                releaseAt: Date.parse(element.dataset.appearAt),
                outputs: element.querySelectorAll('[data-episode-countdown]'),
            }))
            .filter(episode => !Number.isNaN(episode.releaseAt) && episode.outputs.length);

        if (!episodes.length) {
            return;
        }

        // Таймер показывает только дни, часы и минуты, поэтому обновлять DOM каждую секунду не нужно.
        function formatRemaining(milliseconds) {
            const totalMinutes = Math.ceil(milliseconds / 60000);
            const days = Math.floor(totalMinutes / 1440);
            const hours = Math.floor((totalMinutes % 1440) / 60);
            const minutes = totalMinutes % 60;

            return [
                days > 0 ? unitFormatters.day.format(days) : null,
                hours > 0 ? unitFormatters.hour.format(hours) : null,
                unitFormatters.minute.format(minutes),
            ].filter(Boolean).join(' ');
        }

        function updateCountdowns() {
            // Считаем от серверного времени: часы пользователя не должны раньше срока разблокировать серию.
            const now = Date.now() + serverClockOffset;
            let nextUpdateIn = 60000;

            for (const episode of episodes) {
                const remaining = episode.releaseAt - now;

                if (remaining <= 0) {
                    // После выхода Blade должен заново собрать карточку уже с активными действиями.
                    window.location.reload();
                    return;
                }

                const text = formatRemaining(remaining);
                episode.outputs.forEach(output => {
                    output.textContent = text;
                });
                nextUpdateIn = Math.min(nextUpdateIn, remaining);
            }

            window.setTimeout(updateCountdowns, Math.max(1000, nextUpdateIn));
        }

        updateCountdowns();
    }

    function setupBookmarkStates(container) {
        const season = container.dataset.season;

        if (!season) {
            return;
        }

        getEpisodeItems(container).forEach(episode => {
            const episodeNumber = episode.dataset.episodeNumber;
            const toggles = Array.from(episode.querySelectorAll('[data-bookmark-toggle]'));

            if (!episodeNumber || toggles.length === 0) {
                return;
            }

            const storageKey = `${BOOKMARK_STORAGE_PREFIX}:${season}:${episodeNumber}`;

            function renderState(state) {
                const bookmarked = state === 'bookmarked';

                episode.dataset.bookmarkState = state;
                toggles.forEach(toggle => {
                    toggle.classList.toggle('is-bookmarked', bookmarked);
                    toggle.setAttribute('aria-pressed', String(bookmarked));
                    toggle.setAttribute(
                        'aria-label',
                        bookmarked ? 'Удалить серию из закладок' : 'Добавить серию в закладки'
                    );
                    toggle.title = bookmarked ? 'Удалить из закладок' : 'Добавить в закладки';
                });
            }

            function toggleState(event) {
                event.preventDefault();
                event.stopPropagation();

                const imageWrapper = event.currentTarget.closest('.image-wrapper');

                const nextState = episode.dataset.bookmarkState === 'bookmarked'
                    ? 'unbookmarked'
                    : 'bookmarked';

                persistState(storageKey, nextState);
                renderState(nextState);
                pulseInteraction(imageWrapper, 'is-bookmark-interacting');
            }

            bindActivation(toggles, toggleState);

            renderState(readStoredState(storageKey, 'bookmarked', 'unbookmarked'));
        });
    }

    function setupWatchStates(container) {
        const season = container.dataset.season;

        if (!season) {
            return;
        }

        container.querySelectorAll('[data-watch-toggle]').forEach(toggle => {
            const episode = toggle.closest('.episode-cont');
            const episodeNumber = episode?.dataset.episodeNumber;
            const icon = toggle.querySelector('[data-watch-icon]');

            if (!episode || !episodeNumber || !icon) {
                return;
            }

            const storageKey = `${WATCH_STORAGE_PREFIX}:${season}:${episodeNumber}`;

            function renderState(state) {
                const watched = state === 'watched';

                episode.dataset.watchState = state;
                toggle.classList.toggle('is-watched', watched);
                toggle.classList.toggle('is-unwatched', !watched);
                toggle.setAttribute('aria-pressed', String(watched));
                toggle.setAttribute(
                    'aria-label',
                    watched ? 'Отметить серию непросмотренной' : 'Отметить серию просмотренной'
                );
                toggle.title = watched ? 'Просмотрено' : 'Не просмотрено';
                icon.src = watched ? icon.dataset.watchedSrc : icon.dataset.unwatchedSrc;
            }

            function toggleState(event) {
                event.preventDefault();
                event.stopPropagation();

                const imageWrapper = toggle.closest('.image-wrapper');

                const nextState = episode.dataset.watchState === 'watched'
                    ? 'unwatched'
                    : 'watched';

                persistState(storageKey, nextState);
                renderState(nextState);
                pulseInteraction(imageWrapper, 'is-watch-state-interacting');
            }

            bindActivation(toggle, toggleState);

            renderState(readStoredState(storageKey, 'watched', 'unwatched'));
        });
    }

    function getEpisodeItems(container) {
        return Array.from(container.children).filter(item => item.classList.contains('episode-cont'));
    }

    function isUpcomingEpisode(item) {
        return item.dataset.isUpcoming === 'true';
    }

    function getEpisodeNumber(item) {
        return Number(item.dataset.episodeNumber);
    }

    function getEpisodeRating(item) {
        const rating = item.querySelector('.rating-widget');
        const visibleValue = rating?.querySelector('.rating-value')?.textContent;
        const value = Number.parseFloat(visibleValue ?? rating?.dataset.avgRating ?? 0);

        return Number.isNaN(value) ? 0 : value;
    }

    function getOrderedEpisodeItems(items, criterion, direction) {
        const upcomingItems = items
            .filter(isUpcomingEpisode)
            .sort((a, b) => getEpisodeNumber(a) - getEpisodeNumber(b));
        const releasedItems = items.filter(item => !isUpcomingEpisode(item));
        const directionMultiplier = direction === 'descending' ? -1 : 1;

        // Upcoming episodes stay chronological and pinned above the sortable released group.
        releasedItems.sort((a, b) => {
            const primaryA = criterion === 'rating'
                ? getEpisodeRating(a)
                : getEpisodeNumber(a);
            const primaryB = criterion === 'rating'
                ? getEpisodeRating(b)
                : getEpisodeNumber(b);
            const primaryDifference = (primaryA - primaryB) * directionMultiplier;

            if (primaryDifference !== 0) {
                return primaryDifference;
            }

            return (getEpisodeNumber(a) - getEpisodeNumber(b)) * directionMultiplier;
        });

        return [...upcomingItems, ...releasedItems];
    }

    function updateGroupSeparator(container) {
        const visibleItems = getEpisodeItems(container)
            .filter(item => !item.classList.contains('is-collapsed-hidden'));

        getEpisodeItems(container).forEach(item => item.classList.remove('has-group-separator'));

        for (let index = 1; index < visibleItems.length; index++) {
            const previousIsUpcoming = isUpcomingEpisode(visibleItems[index - 1]);
            const currentIsUpcoming = isUpcomingEpisode(visibleItems[index]);

            if (previousIsUpcoming !== currentIsUpcoming) {
                visibleItems[index].classList.add('has-group-separator');
                break;
            }
        }
    }

    function setupEpisodeCollapse(section) {
        const container = section.querySelector('[data-collapsible-episodes]');
        const toggle = section.querySelector('.episode-list-toggle');

        if (!container || !toggle) {
            return;
        }

        const items = getEpisodeItems(container);
        const upcomingItems = items.filter(isUpcomingEpisode);
        const releasedLimit = upcomingItems.length ? 3 : 4;

        getOrderedEpisodeItems(items, 'episode', 'descending')
            .forEach(item => container.append(item));
        container.dataset.sortDirection = 'descending';
        container.dataset.sortCriterion = 'episode';

        const sortButton = section.querySelector('.sort-toggle');
        if (sortButton) {
            updateSortIcons(sortButton, true);
            sortButton.setAttribute('aria-pressed', 'true');
        }

        function getVisibleWhenCollapsed(orderedItems) {
            const orderedUpcoming = orderedItems
                .filter(isUpcomingEpisode);
            const orderedReleased = orderedItems
                .filter(item => !isUpcomingEpisode(item));

            return new Set([
                ...orderedReleased.slice(0, releasedLimit),
                ...orderedUpcoming.slice(0, 1),
            ]);
        }

        let visibleWhenCollapsed = getVisibleWhenCollapsed(getEpisodeItems(container));
        let isToggling = false;

        if (visibleWhenCollapsed.size >= items.length) {
            updateGroupSeparator(container);
            return;
        }

        function updateToggle(expanded) {
            toggle.hidden = false;
            toggle.classList.toggle('is-expanded', expanded);
            toggle.setAttribute('aria-expanded', String(expanded));
            toggle.querySelector('span').textContent = expanded ? 'Свернуть' : 'Развернуть';
        }

        function setItemsVisibility(expanded) {
            items.forEach(item => {
                const hidden = !expanded && !visibleWhenCollapsed.has(item);
                item.classList.toggle('is-collapsed-hidden', hidden);
                item.setAttribute('aria-hidden', String(hidden));
            });

            updateGroupSeparator(container);
        }

        async function setExpanded(expanded, animate = true) {
            if (isToggling) {
                return;
            }

            const shouldAnimate = animate
                && !reducedMotion.matches
                && typeof container.animate === 'function'
                && typeof items[0]?.animate === 'function';

            updateToggle(expanded);

            if (!shouldAnimate) {
                setItemsVisibility(expanded);
                return;
            }

            isToggling = true;
            container.classList.add('is-list-toggling');

            const collapsibleItems = items.filter(item => !visibleWhenCollapsed.has(item));
            const startHeight = container.getBoundingClientRect().height;
            let endHeight;

            if (expanded) {
                setItemsVisibility(true);
                endHeight = container.getBoundingClientRect().height;
            } else {
                setItemsVisibility(false);
                endHeight = container.getBoundingClientRect().height;
                setItemsVisibility(true);
            }

            const containerAnimation = container.animate([
                { height: `${startHeight}px` },
                { height: `${endHeight}px` },
            ], {
                duration: 520,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                fill: 'both',
            });

            const itemAnimations = collapsibleItems.map((item, index) => item.animate(
                expanded
                    ? [
                        { opacity: 0, transform: 'translateY(calc(-1 * var(--fs-gap20)))' },
                        { opacity: 1, transform: 'translateY(0)' },
                    ]
                    : [
                        { opacity: 1, transform: 'translateY(0)' },
                        { opacity: 0, transform: 'translateY(calc(-1 * var(--fs-gap20)))' },
                    ],
                {
                    duration: 360,
                    delay: expanded ? Math.min(index * 25, 150) : 0,
                    easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                    fill: 'both',
                }
            ));

            await Promise.all([
                containerAnimation.finished.catch(() => {}),
                ...itemAnimations.map(animation => animation.finished.catch(() => {})),
            ]);

            setItemsVisibility(expanded);
            containerAnimation.cancel();
            itemAnimations.forEach(animation => animation.cancel());
            container.classList.remove('is-list-toggling');
            isToggling = false;
        }

        toggle.addEventListener('click', () => {
            setExpanded(toggle.getAttribute('aria-expanded') !== 'true');
        });

        container.addEventListener('episodes:order-change', event => {
            visibleWhenCollapsed = getVisibleWhenCollapsed(event.detail.orderedItems);

            if (toggle.getAttribute('aria-expanded') !== 'true') {
                setItemsVisibility(false);
            }
        });

        setExpanded(false, false);
    }

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

    function animateSortedItem(item, firstRect, animateStableItems) {
        const lastRect = item.getBoundingClientRect();
        const offsetX = firstRect.left - lastRect.left;
        const offsetY = firstRect.top - lastRect.top;
        const positionChanged = Math.abs(offsetX) >= 0.5 || Math.abs(offsetY) >= 0.5;

        if (!positionChanged && !animateStableItems) {
            return Promise.resolve();
        }

        if (!positionChanged) {
            item.style.willChange = 'transform, opacity';

            return item.animate([
                {
                    opacity: 0.72,
                    transform: 'translate3d(0, var(--fs-gap10), 0)',
                },
                {
                    opacity: 1,
                    transform: 'translate3d(0, 0, 0)',
                },
            ], {
                duration: 360,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
            }).finished.finally(() => {
                item.style.willChange = '';
            });
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
    }

    function sortGridWithAnimation(
        container,
        sortButton,
        nextDirection,
        nextCriterion = 'episode',
        animateStableItems = false
    ) {
        const items = Array.from(container.children);

        if (sortingContainers.has(container)) {
            return false;
        }

        const isEpisodeList = container.hasAttribute('data-collapsible-episodes');
        const orderedItems = isEpisodeList
            ? getOrderedEpisodeItems(items, nextCriterion, nextDirection)
            : [...items].reverse();

        if (isEpisodeList) {
            container.dispatchEvent(new CustomEvent('episodes:order-change', {
                detail: { orderedItems },
            }));
        }

        const visibleItems = orderedItems
            .filter(item => !item.classList.contains('is-collapsed-hidden'));
        const shouldAnimate = !reducedMotion.matches
            && visibleItems.length > 0
            && typeof visibleItems[0].animate === 'function';
        const firstRects = shouldAnimate
            ? new Map(visibleItems.map((item) => [item, item.getBoundingClientRect()]))
            : null;

        container.style.flexDirection = 'column';
        orderedItems.forEach((item) => container.append(item));
        container.dataset.sortDirection = nextDirection;
        if (isEpisodeList) {
            container.dataset.sortCriterion = nextCriterion;
        }
        updateSortIcons(sortButton, nextDirection === 'descending');
        sortButton.setAttribute('aria-pressed', String(nextDirection === 'descending'));
        if (isEpisodeList) {
            updateGroupSeparator(container);
        }

        if (!shouldAnimate) {
            return true;
        }

        sortingContainers.add(container);
        container.classList.add('is-sorting');

        const animations = visibleItems.map(item => (
            animateSortedItem(item, firstRects.get(item), animateStableItems)
        ));

        Promise.all(animations.map((animation) => animation.catch(() => {}))).then(() => {
            sortingContainers.delete(container);
            container.classList.remove('is-sorting');
        });

        return true;
    }

    document.querySelectorAll('.sort-toggle').forEach((button) => {
        button.addEventListener('click', () => {
            const container = getSortContainer(button);

            if (!container) {
                return;
            }

            const isDescending = container.dataset.sortDirection === 'descending'
                || container.style.flexDirection === 'column-reverse';
            const nextDirection = isDescending ? 'ascending' : 'descending';
            const criterion = container.dataset.sortCriterion || 'episode';

            sortGridWithAnimation(container, button, nextDirection, criterion);
        });
    });

    // Dropdown mechanics are shared; each page supplies only its selection side effects.
    function setupListFilter(filter, onSelect) {
        const toggle = filter.querySelector('.filter-toggle');
        const menu = filter.querySelector('.list-filter-menu');
        const options = Array.from(filter.querySelectorAll('.list-filter-option'));

        if (!toggle || !menu || options.length === 0) {
            return;
        }

        function setOpen(open, focusSelected = false) {
            filter.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', String(open));

            if (open && focusSelected) {
                const selectedOption = options.find(
                    option => option.getAttribute('aria-selected') === 'true'
                );
                (selectedOption || options[0]).focus();
            }
        }

        toggle.addEventListener('click', () => {
            setOpen(toggle.getAttribute('aria-expanded') !== 'true');
        });

        toggle.addEventListener('keydown', event => {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                setOpen(true, true);
            }
        });

        options.forEach(option => {
            option.addEventListener('click', () => {
                if (onSelect?.(option, options) === false) {
                    return;
                }

                setOpen(false);
                toggle.focus();
            });
        });

        menu.addEventListener('keydown', event => {
            const currentIndex = options.indexOf(document.activeElement);
            let nextIndex = null;

            if (event.key === 'ArrowDown') {
                nextIndex = currentIndex < 0 ? 0 : (currentIndex + 1) % options.length;
            } else if (event.key === 'ArrowUp') {
                nextIndex = currentIndex < 0
                    ? options.length - 1
                    : (currentIndex - 1 + options.length) % options.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else if (event.key === 'End') {
                nextIndex = options.length - 1;
            }

            if (nextIndex !== null) {
                event.preventDefault();
                options[nextIndex].focus();
            }
        });

        document.addEventListener('click', event => {
            if (!filter.contains(event.target)) {
                setOpen(false);
            }
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
                setOpen(false);
                toggle.focus();
            }
        });
    }

    function setupEpisodeFilter(filter) {
        const section = filter.closest('.cont2');
        const container = section?.querySelector('[data-collapsible-episodes]');
        const sortButton = section?.querySelector('.sort-toggle');

        if (!container || !sortButton) {
            return;
        }

        setupListFilter(filter, (option, options) => {
            const criterion = option.dataset.sortCriterion;
            const currentCriterion = container.dataset.sortCriterion || 'episode';
            const direction = container.dataset.sortDirection || 'descending';

            if (criterion !== currentCriterion
                && !sortGridWithAnimation(container, sortButton, direction, criterion, true)) {
                return false;
            }

            options.forEach(item => {
                const selected = item === option;
                item.classList.toggle('is-selected', selected);
                item.setAttribute('aria-selected', String(selected));
            });

            return true;
        });
    }

    document.querySelectorAll('[data-list-filter="episodes"]').forEach(setupEpisodeFilter);
    document.querySelectorAll('[data-list-filter="chapters"]').forEach(filter => {
        setupListFilter(filter);
    });

    document.querySelectorAll('[data-open-in-new-tab]').forEach(icon => {
        bindActivation(icon, event => {
            event.preventDefault();
            event.stopPropagation();

            const cardLink = icon.closest('.card-link');
            if (cardLink?.href) {
                window.open(cardLink.href, '_blank', 'noopener,noreferrer');
            }
        });
    });

    document.querySelectorAll('[data-collapsible-episodes]').forEach(container => {
        setupDisabledEpisodeControls(container);
        setupEpisodeCountdowns(container);
        setupBookmarkStates(container);
        setupWatchStates(container);
    });
    document.querySelectorAll('.cont2').forEach(setupEpisodeCollapse);
});
