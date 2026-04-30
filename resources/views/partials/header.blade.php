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
                <a href="{{ route('login') }}" class="link-like-button login-btn">АККАУНТ</a>
            @endauth
        </div>
    </div>
    <div class="side-menu" id="sideMenu">
        <div class="side-links">
            <button disabled>НОВОСТИ</button>
            <button disabled>РАНОБЕ</button>
            <button>АНИМЕ</button>
            <button disabled>МАНГА</button>
            <button disabled>ИЛЛЮСТРАЦИИ</button>
            <button disabled>ПЕРСОНАЖИ</button>
            <button>О ПРОЕКТЕ</button>
            <button class="closeMenu">ЗАКРЫТЬ</button>
        </div>
    </div>
    <div class="overlay" id="overlay"></div>
</nav>