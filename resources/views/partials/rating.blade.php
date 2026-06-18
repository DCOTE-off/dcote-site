<div class="rating-widget"
     data-rateable-type="{{ $rateableType }}"
     data-rateable-id="{{ $rateableId }}"
     data-user-rating="{{ $userRating ?? 0 }}"
     data-avg-rating="{{ $avgRating ?? 0 }}"
     data-ratings-count="{{ $ratingsCount ?? 0 }}">
    <div class="star-and-avg">
        @if (!($visualOnly ?? false))
            <button class="button-without-styles-all toggle-rating-menu-btn">
                <svg class="star-rating-icon" viewBox="0 0 36 35">
                    <use href="#star"></use>
                </svg>
            </button>
        @else
            <svg class="star-rating-icon visual" viewBox="0 0 36 35">
                <use href="#star"></use>
            </svg>
        @endif
        <h3 class="rating-value">{{ $avgRating ?? '0' }}</h3>
        <div class="rating-popup-menu">
            <div class="stars-row"></div>
            <div class="rating-popup-info">
                    <p>Всего оценок: <b class="ratings-count">{{ $ratingsCount ?? 0 }}</b></p>
                @auth
                    <p>Ваша оценка: <b class="your-choosen-rating"></b></p>
                @endauth
            </div>
            @guest
                <div class="popup-overlay">
                    <a href="{{ route('login') }}">
                        <b><p>Войдите в аккаунт, чтобы поставить оценку</p></b>
                    </a>
                </div>
            @endguest
        </div>
    </div>
    @if (!($noExtra ?? false))
    <div class="extra-info">
        <p>Всего оценок: <span class="ratings-count">{{ $ratingsCount ?? 0 }}</span></p>
    </div>
    @endif
</div>
