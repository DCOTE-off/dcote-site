document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.sort-toggle')?.addEventListener('click', function() {
        const cont = document.querySelector('.grid-area');
        const sortAsc = this.querySelector('.sort-ascending');
        const sortDesc = this.querySelector('.sort-descending');
        const isReversed = cont.style.flexDirection === 'column-reverse';
        cont.style.flexDirection = isReversed ? 'column' : 'column-reverse';

        if (sortAsc && sortDesc) {
            sortAsc.style.display = isReversed ? 'block' : 'none';
            sortDesc.style.display = isReversed ? 'none' : 'block';
        }

        this.setAttribute('aria-label', isReversed ? 'Сортировка: по возрастанию' : 'Сортировка: по убыванию');
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
})