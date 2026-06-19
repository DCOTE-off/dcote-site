document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.rating-widget[data-rateable-type]').forEach(widget => {
        const toggleBtn = widget.querySelector('.toggle-rating-menu-btn');
        const ratingPopup = widget.querySelector('.rating-popup-menu');
        const starsRow = widget.querySelector('.stars-row');
        const choosenEl = widget.querySelector('.your-choosen-rating');
        const valueEl = widget.querySelector('.rating-value');
        const countEl = widget.querySelector('.ratings-count');
        const rateableType = widget.dataset.rateableType;
        const rateableId = widget.dataset.rateableId;

        if (!starsRow) return;

        const STAR_COUNT = 10;

        function getUserRating() {
            return parseInt(widget.dataset.userRating) || 0;
        }

        function fillStars(upTo) {
            starsRow.querySelectorAll('.star-value-icon').forEach(icon => {
                const val = +icon.closest('.star-value-button').dataset.value;
                icon.classList.toggle('filled', val <= upTo);
            });
        }

        function pickStar(upTo) {
            starsRow.querySelectorAll('.star-value-button').forEach(btn => {
                btn.classList.toggle('picked', +btn.dataset.value === upTo);
            });
        }

        for (let i = 1; i <= STAR_COUNT; i++) {
            const btn = document.createElement('button');
            btn.className = 'button-without-styles-all star-value-button';
            btn.dataset.value = i;

            btn.innerHTML = `
                <svg class="star-rating-icon star-value-icon" viewBox="0 0 36 35">
                    <use href="#star"></use>
                </svg>
            `;

            btn.addEventListener('mouseenter', () => {
                fillStars(i);
                pickStar(i);
                if (choosenEl) choosenEl.textContent = i;
            });

            btn.addEventListener('click', () => {
                const clicked = parseInt(btn.dataset.value);
                if (clicked === getUserRating()) {
                    sendRating(rateableType, rateableId, 0, widget, valueEl, countEl);
                } else {
                    sendRating(rateableType, rateableId, clicked, widget, valueEl, countEl);
                }
            });

            starsRow.appendChild(btn);
        }

        starsRow.addEventListener('mouseleave', () => {
            const userRating = getUserRating();
            fillStars(userRating);
            pickStar(userRating);
            if (choosenEl) choosenEl.textContent = userRating || '';
        });

        if (getUserRating()) {
            fillStars(getUserRating());
            pickStar(getUserRating());
            if (toggleBtn) toggleBtn.classList.add('has-rating');
        }

        function closePopup() {
            ratingPopup.classList.remove('is-open');
            toggleBtn.classList.remove('active');
        }

        if (toggleBtn && ratingPopup) {
            toggleBtn.addEventListener('click', e => {
                e.stopPropagation();

                if (!ratingPopup.classList.contains('is-open')) {
                    document.querySelectorAll('.rating-widget[data-rateable-type]').forEach(other => {
                        const otherPopup = other.querySelector('.rating-popup-menu');
                        const otherToggle = other.querySelector('.toggle-rating-menu-btn');
                        if (otherPopup && otherPopup !== ratingPopup && otherPopup.classList.contains('is-open')) {
                            otherPopup.classList.remove('is-open');
                            otherToggle.classList.remove('active');
                        }
                    });
                }

                ratingPopup.classList.toggle('is-open');
                toggleBtn.classList.toggle('active');
                if (ratingPopup.classList.contains('is-open')) {
                    const userRating = getUserRating();
                    fillStars(userRating);
                    pickStar(userRating);
                    if (choosenEl && userRating) {
                        choosenEl.textContent = userRating;
                    }
                }
            });

            document.addEventListener('click', e => {
                if (ratingPopup.classList.contains('is-open') && !widget.contains(e.target)) {
                    closePopup();
                }
            });
        }
    });
});

function sendRating(type, id, value, widget, valueEl, countEl) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) return;

    animateRatingIcon(widget);

    const method = value ? 'POST' : 'DELETE';
    const body = value
        ? JSON.stringify({ rateable_type: type, rateable_id: id, rating: value })
        : JSON.stringify({ rateable_type: type, rateable_id: id });

    fetch('/api/ratings', {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body,
    })
        .then(res => res.json())
        .then(data => {
            const previousAverage = Number.parseFloat(valueEl.textContent) || 0;
            const nextAverage = Number(data.avg_rating ?? 0);
            const formattedAverage = nextAverage.toFixed(1);

            widget.dataset.userRating = data.user_rating;

            if (previousAverage !== nextAverage) {
                animateRatingValue(valueEl, previousAverage.toFixed(1), formattedAverage, nextAverage > previousAverage);
            } else {
                valueEl.textContent = formattedAverage;
            }

            if (data.ratings_count !== undefined) {
                widget.querySelectorAll('.ratings-count').forEach(el => {
                    el.textContent = data.ratings_count;
                });
            }

            const starsRow = widget.querySelector('.stars-row');
            if (starsRow) {
                starsRow.querySelectorAll('.star-value-icon').forEach(icon => {
                    const val = +icon.closest('.star-value-button').dataset.value;
                    icon.classList.toggle('filled', val <= data.user_rating);
                });
                starsRow.querySelectorAll('.star-value-button').forEach(btn => {
                    btn.classList.toggle('picked', +btn.dataset.value === data.user_rating);
                });
            }

            const toggle = widget.querySelector('.toggle-rating-menu-btn');

            if (data.user_rating) {
                toggle.classList.add('has-rating');
            } else {
                toggle.classList.remove('has-rating');
            }
        })
        .catch(() => {});
}

function animateRatingIcon(widget) {
    const toggle = widget.querySelector('.toggle-rating-menu-btn');

    if (!toggle) return;

    toggle.classList.remove('is-rating-updated');
    void toggle.offsetWidth;
    toggle.classList.add('is-rating-updated');
    toggle.addEventListener('animationend', () => {
        toggle.classList.remove('is-rating-updated');
    }, { once: true });
}

function animateRatingValue(valueEl, previousValue, nextValue, isIncreasing) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        valueEl.textContent = nextValue;
        return;
    }

    valueEl.classList.remove('is-changing');
    valueEl.dataset.direction = isIncreasing ? 'up' : 'down';
    valueEl.innerHTML = `
        <span class="rating-value-current">${previousValue}</span>
        <span class="rating-value-next">${nextValue}</span>
    `;
    void valueEl.offsetWidth;
    valueEl.classList.add('is-changing');

    const nextValueEl = valueEl.querySelector('.rating-value-next');
    nextValueEl.addEventListener('animationend', () => {
        valueEl.classList.remove('is-changing');
        delete valueEl.dataset.direction;
        valueEl.textContent = nextValue;
    }, { once: true });
}
