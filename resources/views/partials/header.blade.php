<nav>
    <div class="navbar">
        <div class="left">
            <a href="{{ route('home') }}">
                <svg class="main-logo"><use href="#dcote-svg"></use></svg>
            </a>
        </div>
        <div class="center">
            <a class="disabled-link"><span>НОВОСТИ</span></a>
            <a href="{{ route('ranobe.index') }}"><span>РАНОБЭ</span></a>
            <a href="{{ route('anime.index') }}"><span>АНИМЕ</span></a>
            <a class="disabled-link"><span>МАНГА</span></a>
            <a class="disabled-link"><span>ИЛЛЮСТРАЦИИ</span></a>
            <a class="disabled-link"><span>ПЕРСОНАЖИ</span></a>
            <a href="{{ route('about-project') }}"><span>О ПРОЕКТЕ</span></a>
        </div>
        <div class="right-wrapper">
            <div class="right">
                <a href="#" class="mail-link" aria-label="Почта">
                    <svg class="mail-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <use href="#mail-icon"></use>
                    </svg>
                </a>
                @auth
                    <button class="login-btn account-dropdown-btn">
                        АККАУНТ
                    </button>
                @else
                    <a href="{{ route('login') }}" class="link-like-button login-btn">ВОЙТИ</a>
                @endauth
            </div>
        </div>
    </div>
    <div class="account-dropdown-any">
        <a class="link-like-button side-button" href="{{ route('account') }}">
            <svg class="account-desktop-menu-icon">
                <use href="#user"></use>
            </svg>
            Мой профиль
        </a>
        <a class="link-like-button side-button" id="account-dd-fav-item" style="background: #c6750c;box-shadow: 0 0px clamp(10px, 1.3vw, 20px) 0px #c6750c;" href="{{ route('favorite') }}">
            <svg class="account-desktop-menu-icon">
                <use href="#file-star"></use>
            </svg>
            Избранное
        </a>
        <a class="link-like-button no-glow side-button" href="{{ route('rules') }}">
            <svg class="account-desktop-menu-icon">
                <use href="#info"></use>
            </svg>
            Правила сайта
        </a>
        <a class="link-like-button no-glow side-button" href="{{ route('settings') }}">
            <svg class="account-desktop-menu-icon">
                <use href="#settings"></use>
            </svg>
            Настройки
        </a>
        @can('access-admin')
            <a class="link-like-button side-button" style="background:#6a3fbf;box-shadow: 0 0px clamp(10px, 1.3vw, 20px) 0px #6a3fbf" href="admin">
                <svg class="account-desktop-menu-icon">
                    <use href="#database"></use>
                </svg>
                Админ-панель
            </a>
        @endcan
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="no-glow side-button">
                <svg class="account-desktop-menu-icon">
                    <use href="#exit"></use>
                </svg>
                Выйти с аккаунта
            </button>
        </form>
    </div>
    <div class="side-menu" id="sideMenu">
        <div class="side-links">
            <a class="link-like-button side-button" href="{{ route('ranobe.index') }}">
                <svg class="side-menu-icon">
                    <use href="#side-menu-ranobe"></use>
                </svg>
                РАНОБЭ
            </a>
            <a class="link-like-button side-button" href="{{ route('anime.index') }}">
                <svg class="side-menu-icon">
                    <use href="#side-menu-anime"></use>
                </svg>
                АНИМЕ
            </a>
            <a class="link-like-button no-glow side-button" href="{{ route('about-project') }}">
                <svg class="side-menu-icon">
                    <use href="#side-menu-about"></use>
                </svg>
                О ПРОЕКТЕ
            </a>
            <a class="link-like-button no-glow disabled_a side-button" aria-disabled="true">
                <svg class="side-menu-icon">
                    <use href="#side-menu-news"></use>
                </svg>
                НОВОСТИ
            </a>
            <a class="link-like-button no-glow disabled_a side-button" aria-disabled="true">
                <svg class="side-menu-icon">
                    <use href="#side-menu-manga"></use>
                </svg>
                МАНГА
            </a>
            <a class="link-like-button no-glow disabled_a side-button" aria-disabled="true">
                <svg class="side-menu-icon">
                    <use href="#side-menu-illustrations"></use>
                </svg>
                ИЛЛЮСТРАЦИИ
            </a>
            <a class="link-like-button no-glow disabled_a side-button" aria-disabled="true">
                <svg class="side-menu-icon">
                    <use href="#side-menu-characters"></use>
                </svg>
                ПЕРСОНАЖИ
            </a>
        </div>
    </div>
</nav>
