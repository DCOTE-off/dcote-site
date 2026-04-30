<svg style="display: none;">
    <symbol id="star" viewBox="0 0 24 24">
        <path fill="currentColor" d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" />
    </symbol>
    <symbol id="eye-filled" viewBox="0 0 24 24">
        <path fill="currentColor" d="M12 4c4.29 0 7.863 2.429 10.665 7.154l.22 .379l.045 .1l.03 .083l.014 .055l.014 .082l.011 .1v.11l-.014 .111a.992 .992 0 0 1 -.026 .11l-.039 .108l-.036 .075l-.016 .03c-2.764 4.836 -6.3 7.38 -10.555 7.499l-.313 .004c-4.396 0 -8.037 -2.549 -10.868 -7.504a1 1 0 0 1 0 -.992c2.831 -4.955 6.472 -7.504 10.868 -7.504zm0 5a3 3 0 1 0 0 6a3 3 0 0 0 0 -6" />
    </symbol>
    <symbol id="sort-descending-filled" viewBox="0 0 24 24">
        <path fill="currentColor" d="M9.5 4a1.5 1.5 0 0 1 1.5 1.5v4a1.5 1.5 0 0 1 -1.5 1.5h-4a1.5 1.5 0 0 1 -1.5 -1.5v-4a1.5 1.5 0 0 1 1.5 -1.5z" />
        <path fill="currentColor" d="M9.5 13a1.5 1.5 0 0 1 1.5 1.5v4a1.5 1.5 0 0 1 -1.5 1.5h-4a1.5 1.5 0 0 1 -1.5 -1.5v-4a1.5 1.5 0 0 1 1.5 -1.5z" />
        <path fill="currentColor" d="M17 5a1 1 0 0 1 1 1v9.584l1.293 -1.291a1 1 0 0 1 1.32 -.083l.094 .083a1 1 0 0 1 0 1.414l-3 3a1 1 0 0 1 -.112 .097l-.11 .071l-.114 .054l-.105 .035l-.149 .03l-.117 .006l-.075 -.003l-.126 -.017l-.111 -.03l-.111 -.044l-.098 -.052l-.096 -.067l-.09 -.08l-3 -3a1 1 0 0 1 1.414 -1.414l1.293 1.293v-9.586a1 1 0 0 1 1 -1" />
    </symbol>
    <symbol id="sort-ascending-filled" viewBox="0 0 24 24">
        <path fill="currentColor" d="M16.852 5.011l.058 -.007l.09 -.004l.075 .003l.126 .017l.111 .03l.111 .044l.098 .052l.104 .074l.082 .073l3 3a1 1 0 1 1 -1.414 1.414l-1.293 -1.292v9.585a1 1 0 0 1 -2 0v-9.585l-1.293 1.292a1 1 0 0 1 -1.32 .083l-.094 -.083a1 1 0 0 1 0 -1.414l3 -3q .053 -.054 .112 -.097l.11 -.071l.114 -.054l.105 -.035z" />
        <path fill="currentColor" d="M9.5 4a1.5 1.5 0 0 1 1.5 1.5v4a1.5 1.5 0 0 1 -1.5 1.5h-4a1.5 1.5 0 0 1 -1.5 -1.5v-4a1.5 1.5 0 0 1 1.5 -1.5z" />
        <path fill="currentColor" d="M9.5 13a1.5 1.5 0 0 1 1.5 1.5v4a1.5 1.5 0 0 1 -1.5 1.5h-4a1.5 1.5 0 0 1 -1.5 -1.5v-4a1.5 1.5 0 0 1 1.5 -1.5z" />
    </symbol>
    <symbol id="message-filled" viewBox="0 0 24 24">
        <path fill="currentColor" d="M18 3a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-4.724l-4.762 2.857a1 1 0 0 1 -1.508 -.743l-.006 -.114v-2h-1a4 4 0 0 1 -3.995 -3.8l-.005 -.2v-8a4 4 0 0 1 4 -4zm-4 9h-6a1 1 0 0 0 0 2h6a1 1 0 0 0 0 -2m2 -4h-8a1 1 0 1 0 0 2h8a1 1 0 0 0 0 -2" />
    </symbol>
</svg>
<?php
$about_season = execute_query('SELECT img_src,season_description,trailer_link FROM anime_seasons WHERE season_number=?', [$season], true);
$episodes = execute_query('SELECT * FROM anime_episodes WHERE number_of_season=? ORDER BY episode_number DESC ', [$season], fetch: 'all');
?>
<div class="navigation-links">
    <a href="/"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a href="/anime"><span>АНИМЕ</span></a>
    <p>/</p>
    <a class="current-page" href="/anime/<?= $season ?>"><span><?= $season ?> СЕЗОН</span></a>
</div>
<main class="page-anime-season">
    <div class="cont scale-in">
        <div class="image-wrapper">
            <img src="/images/anime/anime-banner-season-<?=$season ?>.webp">
        </div>
        <div class="desc slide-in-left">
            <h1><?= $season ?> СЕЗОН АНИМЕ-АДАПТАЦИИ</h1>
            <p><?= !empty(formatHtmlSafe($about_season['season_description'])) ? formatHtmlSafe($about_season['season_description']) : 'Описание сезона' ?></p>
            <div class="low-buttons "><a href="<?= ($about_season['trailer_link']) ?>" style="box-shadow:none;" class="link-like-button">СМОТРЕТЬ ТРЕЙЛЕР</a><a href="<?= $season ?>/1" class="link-like-button">НАЧАТЬ ПРОСМОТР</a></div>
            <div class="low-buttons mobile"><a href="<?= ($about_season['trailer_link']) ?>" style="box-shadow:none;" class="link-like-button">ТРЕЙЛЕР</a><a href="<?= $season ?>/1" class="link-like-button">СМОТРЕТЬ С 1 СЕРИИ</a></div>
        </div>
    </div>
    <div class="cont2 scale-in">
        <div class="episodes-head">
            <h1>СПИСОК СЕРИЙ</h1>
            <button type="button" class="sort-toggle button-without-styles" aria-label="Сортировать по возрастанию/убыванию">
                <svg class="sort-descending" width="20" height="20" style="display: none;">
                    <use href="#sort-descending-filled"></use>
                </svg>
                <svg class="sort-ascending" width="20" height="20">
                    <use href="#sort-ascending-filled"></use>
                </svg>
            </button>
        </div>
        <div class="grid-area">
            <?php if (!empty($episodes)): ?>
                <?php foreach ($episodes as $index => $episode): ?>
                    <div class="episode-cont">
                        <svg class="eye-filled" width="30" height="30">
                            <use href="#eye-filled"></use>
                        </svg>
                        <a class="card-link" href="/anime/<?= $season ?>/<?= e($episode['episode_number']) ?>">
                            <div class="card-title mobile">
                                <h3><?= e($episode['episode_number']) ?> серия</h3>
                                <p><?= e($episode['episode_name']) ?></p>
                            </div>
                            <div class="image-wrapper"><img src="/images/anime/episodes-banner-season<?= $season ?>.webp"></div>
                            <div class="card-title">
                                <h3><?= e($episode['episode_number']) ?> серия</h3>
                                <p><?= e($episode['episode_name']) ?></p>
                            </div>
                        </a>
                        <div class="stars-and-comms">
                            <div class="first-btn">
                                <div class="rating">
                                    <div class="popup-stars hidden">
                                        <?php for ($i = 1; $i <= 10; $i++): ?>
                                            <div class="popup-stars-column"><button class="rating-btn button-without-styles" data-rating="<?= $i ?>" aria-label="оценка"><svg class="star-icon" width="30" height="30">
                                                        <use href="#star"></use>
                                                    </svg></button>
                                                <p><?= $i ?></p>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="star-and-number"><button class="rating-btn-for-popup button-without-styles" aria-label="оценка"><svg class="star-icon" width="30" height="30">
                                                <use href="#star"></use>
                                            </svg></button>
                                        <h3>9</h3>
                                    </div>
                                </div>
                            </div>
                            <svg class="eye-filled mobile" width="30" height="30">
                                <use href="#eye-filled"></use>
                            </svg>
                            <div class="second-btn"><button class="message-btn button-without-styles"><svg class="message-icon" width="30" height="30">
                                        <use href="#message-filled"></use>
                                    </svg></button>
                                <h3>9</h3>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
<script>
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
</script>

</html>