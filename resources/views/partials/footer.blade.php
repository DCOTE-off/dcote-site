    <footer class="footer-nav">
        <div class="footer-left">
            <a href="https://x.com/aandreev06" class="logo-link "><svg class="social-nets-logos" width="50" height="50"><use href="#x-logo"></use></svg></a>
            <a href="/" class="logo-link "><svg class="social-nets-logos" width="50" height="50"><use href="#ds-logo"></use></svg></a>
            <a href="https://t.me/DCOTEFILES" class="logo-link "><svg class="social-nets-logos" width="50" height="50"><use href="#tg-logo"></use></svg></a>
        </div>
        <div class="footer-center">
            <p>Мы не претендуем на авторство и/или какие-либо иные права на какой-либо контент с авторским правом, представленный на сайте.</p>
        </div>
        <div class="footer-right">
            <a href="/"><svg class="main-logo"><use href="#dcote-svg"></use></svg></a>
        </div>
    </footer>
    <navbar class="mobile-bottom-nav">
        <a href="{{ route('favorite') }}" class="nav-item">
            <svg class="nav-icon"><use href="#file-star"></use></svg>
            <h2 class="nav-label">Избранное</h2>
        </a>
        @auth
                    <a href="#" class="nav-item">
                        <svg class="nav-icon"><use href="#user"></use></svg>
                        <h2 class="nav-label">Аккаунт</h2>
                    </a>
                    <div class="dropdown" data-dropdown>
                        <div class="drop-menu" data-dropdown-menu>
                            <a href="{{ route('account') }}"><img src="/images/menu/user.svg" alt="avatar">Мой аккаунт</a>
                            <a href="{{ route('favorite') }}"><img src="/images/menu/file-star.svg" alt="avatar">Избранное</a>
                            <a href="{{ route('rules') }}"><img src="/images/menu/info-square.svg" alt="avatar">Правила сайта</a>
                            <a href="{{ route('settings') }}"><img src="/images/menu/settings.svg" alt="avatar">Настройки</a>
                            <a href="{{ route('logout') }}"><img src="/images/menu/layout-sidebar-right-expand.svg" alt="avatar">Выйти с аккаунта</a>
                        </div>
                    </div>
                @else
                    <a href="/login" class="nav-item">
                        <svg class="nav-icon"><use href="#user"></use></svg>
                        <h2 class="nav-label">Аккаунт</h2>
                    </a>
                @endauth
        <a href="/" class="nav-item">
            <svg class="center-nav-icon"><use href="#dcote-logo-small"></use></svg>
        </a>
        <a href="/favorite" class="nav-item">
            <svg class="nav-icon"><use href="#mail"></use></svg>
            <h2 class="nav-label">Уведомления</h2>
        </a>
        <button class="nav-item button-without-styles" id="hamburgerBtn">
            <svg class="nav-icon"><use href="#stack-2"></use></svg>
            <h2 class="nav-label">Меню</h2>
        </button>
    </navbar>
    <div id="notification-container" class="hidden">
        <h1>ОШИБКА</h1>
        <p></p>
        <div class="shape-close"><svg class="close-icon" stroke-width="3" width="24" height="24"><use href="#close-cross"></use></svg></div>
    </div>