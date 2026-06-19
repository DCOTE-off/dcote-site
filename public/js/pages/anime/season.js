document.addEventListener('DOMContentLoaded', () => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const sortingContainers = new WeakSet();
    const WATCH_STORAGE_PREFIX = 'dcote:anime:watch-state:v1';
    const BOOKMARK_STORAGE_PREFIX = 'dcote:anime:bookmark-state:v1';

    function setupDisabledEpisodeControls(container) {
        const selector = [
            '.episode-watch-state[aria-disabled="true"]',
            '.episode-bookmark-toggle[aria-disabled="true"]',
        ].join(', ');

        container.querySelectorAll(selector).forEach(control => {
            function blockDisabledControl(event) {
                event.preventDefault();
                event.stopPropagation();
            }

            control.addEventListener('click', blockDisabledControl);
            control.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    blockDisabledControl(event);
                }
            });
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

            function readStoredState() {
                try {
                    return localStorage.getItem(storageKey) === 'bookmarked'
                        ? 'bookmarked'
                        : 'unbookmarked';
                } catch {
                    return 'unbookmarked';
                }
            }

            function persistState(state) {
                try {
                    localStorage.setItem(storageKey, state);
                } catch {
                    // Replace with the bookmark API when server persistence is implemented.
                }
            }

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
                imageWrapper?.classList.add('is-bookmark-interacting');

                const nextState = episode.dataset.bookmarkState === 'bookmarked'
                    ? 'unbookmarked'
                    : 'bookmarked';

                persistState(nextState);
                renderState(nextState);

                window.setTimeout(() => {
                    imageWrapper?.classList.remove('is-bookmark-interacting');
                }, 350);
            }

            toggles.forEach(toggle => {
                toggle.addEventListener('click', toggleState);
                toggle.addEventListener('keydown', event => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        toggleState(event);
                    }
                });
            });

            renderState(readStoredState());
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

            function readStoredState() {
                try {
                    return localStorage.getItem(storageKey) === 'watched' ? 'watched' : 'unwatched';
                } catch {
                    return 'unwatched';
                }
            }

            function persistState(state) {
                try {
                    localStorage.setItem(storageKey, state);
                } catch {
                    // localStorage may be unavailable in private or restricted contexts.
                }
            }

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
                imageWrapper?.classList.add('is-watch-state-interacting');

                const nextState = episode.dataset.watchState === 'watched'
                    ? 'unwatched'
                    : 'watched';

                persistState(nextState);
                renderState(nextState);

                window.setTimeout(() => {
                    imageWrapper?.classList.remove('is-watch-state-interacting');
                }, 350);
            }

            toggle.addEventListener('click', toggleState);
            toggle.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    toggleState(event);
                }
            });

            renderState(readStoredState());
        });
    }

    function getEpisodeItems(container) {
        return Array.from(container.children).filter(item => item.classList.contains('episode-cont'));
    }

    function updateGroupSeparator(container) {
        const visibleItems = getEpisodeItems(container)
            .filter(item => !item.classList.contains('is-collapsed-hidden'));

        getEpisodeItems(container).forEach(item => item.classList.remove('has-group-separator'));

        for (let index = 1; index < visibleItems.length; index++) {
            const previousIsUpcoming = visibleItems[index - 1].dataset.isUpcoming === 'true';
            const currentIsUpcoming = visibleItems[index].dataset.isUpcoming === 'true';

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
        const upcomingItems = items
            .filter(item => item.dataset.isUpcoming === 'true')
            .sort((a, b) => Number(a.dataset.episodeNumber) - Number(b.dataset.episodeNumber));
        const releasedItems = items
            .filter(item => item.dataset.isUpcoming !== 'true')
            .sort((a, b) => Number(a.dataset.episodeNumber) - Number(b.dataset.episodeNumber));
        const releasedLimit = upcomingItems.length ? 3 : 4;

        [...upcomingItems, ...releasedItems.slice().reverse()].forEach(item => container.append(item));
        container.dataset.sortDirection = 'descending';

        const sortButton = section.querySelector('.sort-toggle');
        if (sortButton) {
            updateSortIcons(sortButton, true);
            sortButton.setAttribute('aria-pressed', 'true');
        }

        function getVisibleWhenCollapsed(edge) {
            const visibleReleased = edge === 'earliest'
                ? releasedItems.slice(0, releasedLimit)
                : releasedItems.slice(-releasedLimit);

            return new Set([
                ...visibleReleased,
                ...upcomingItems.slice(0, 1),
            ]);
        }

        let visibleWhenCollapsed = getVisibleWhenCollapsed('latest');
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

        container.addEventListener('episodes:sort-edge', event => {
            visibleWhenCollapsed = getVisibleWhenCollapsed(event.detail.edge);

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

    function sortGridWithAnimation(container, button) {
        const items = Array.from(container.children);

        if (items.length < 2 || sortingContainers.has(container)) {
            return;
        }

        const isDescending = container.dataset.sortDirection === 'descending'
            || container.style.flexDirection === 'column-reverse';
        const nextDirection = isDescending ? 'ascending' : 'descending';
        const isEpisodeList = container.hasAttribute('data-collapsible-episodes');

        if (isEpisodeList) {
            container.dispatchEvent(new CustomEvent('episodes:sort-edge', {
                detail: {
                    edge: nextDirection === 'descending' ? 'latest' : 'earliest',
                },
            }));
        }

        const visibleItems = items.filter(item => !item.classList.contains('is-collapsed-hidden'));
        const shouldAnimate = !reducedMotion.matches
            && visibleItems.length > 0
            && typeof visibleItems[0].animate === 'function';
        const firstRects = shouldAnimate
            ? new Map(visibleItems.map((item) => [item, item.getBoundingClientRect()]))
            : null;
        const orderedItems = isEpisodeList
            ? [
                ...items.filter(item => item.dataset.isUpcoming === 'true'),
                ...items.filter(item => item.dataset.isUpcoming !== 'true').reverse(),
            ]
            : [...items].reverse();

        container.style.flexDirection = 'column';
        orderedItems.forEach((item) => container.append(item));
        container.dataset.sortDirection = nextDirection;
        updateSortIcons(button, !isDescending);
        button.setAttribute('aria-pressed', String(!isDescending));
        if (isEpisodeList) {
            updateGroupSeparator(container);
        }

        if (!shouldAnimate) {
            return;
        }

        sortingContainers.add(container);
        container.classList.add('is-sorting');

        const animations = visibleItems.map((item) => {
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

    document.querySelectorAll('[data-open-in-new-tab]').forEach(icon => {
        function openInNewTab(event) {
            event.preventDefault();
            event.stopPropagation();

            const cardLink = icon.closest('.card-link');
            if (cardLink?.href) {
                window.open(cardLink.href, '_blank', 'noopener,noreferrer');
            }
        }

        icon.addEventListener('click', openInNewTab);
        icon.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                openInNewTab(event);
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
