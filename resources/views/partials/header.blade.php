<nav>
    <div class="navbar">
        <div class="left">
            <a href="{{ route('home') }}">
                <svg class="main-logo"><use href="#dcote-svg"></use></svg>
            </a>
        </div>
        <div class="center">
            <a><span>НОВОСТИ</span></a>
            <a><span>РАНОБЕ</span></a>
            <a href="{{ route('anime.index') }}"><span>АНИМЕ</span></a>
            <a><span>МАНГА</span></a>
            <a><span>ИЛЛЮСТРАЦИИ</span></a>
            <a><span>ПЕРСОНАЖИ</span></a>
            <a href="{{ route('about-project') }}"><span>О ПРОЕКТЕ</span></a>
        </div>
        <div class="right">
            @auth
                <div class="dropdown" data-dropdown>
                    <button class="link-like-button login-btn" data-dropdown-toggle>
                        АККАУНТ
                    </button>
                </div>
            @else
                <a href="{{ route('login') }}" class="link-like-button disabled_a login-btn">АККАУНТ</a>
            @endauth
        </div>
    </div>
    <div class="side-menu" id="sideMenu">
        <div class="side-links">
            <a class="link-like-button no-glow side-button" href="{{ route('anime.index') }}">
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
            <a class="link-like-button no-glow side-button" href="{{ route('news.index') }}">
                <svg class="side-menu-icon">
                    <use href="#side-menu-news"></use>
                </svg>
                НОВОСТИ
            </a>
            <a class="link-like-button no-glow side-button" href="{{ route('ranobe.index') }}">
                <svg class="side-menu-icon">
                    <use href="#side-menu-ranobe"></use>
                </svg>
                РАНОБЕ
            </a>
            <a class="link-like-button no-glow side-button" href="{{ route('manga.index') }}">
                <svg class="side-menu-icon">
                    <use href="#side-menu-manga"></use>
                </svg>
                МАНГА
            </a>
            <a class="link-like-button no-glow side-button" href="{{ route('illustrations.index') }}">
                <svg class="side-menu-icon">
                    <use href="#side-menu-illustrations"></use>
                </svg>
                ИЛЛЮСТРАЦИИ
            </a>
            <a class="link-like-button no-glow side-button" href="{{ route('characters.index') }}">
                <svg class="side-menu-icon">
                    <use href="#side-menu-characters"></use>
                </svg>
                ПЕРСОНАЖИ
            </a>
        </div>
    </div>
</nav>

