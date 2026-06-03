    <footer class="footer-nav">
        <div class="footer-left">
            <a href="https://x.com/aandreev06" class="logo-link "><svg class="social-nets-logos" width="50" height="50"><use href="#x-logo"></use></svg></a>
            <a href="https://discord.gg/cTTwcYhbR" class="logo-link "><svg class="social-nets-logos" width="50" height="50"><use href="#ds-logo"></use></svg></a>
            <a href="https://t.me/DCOTEFILES" class="logo-link "><svg class="social-nets-logos" width="50" height="50"><use href="#tg-logo"></use></svg></a>
        </div>
        <div class="footer-center">
            <p>Мы не претендуем на авторство и/или какие-либо иные права на какой-либо контент с авторским правом, представленный на сайте.</p>
            <div class="footer-links">
                <a href="#">Политика конфеденциальности</a>
                <a href="{{ route('rules') }}">Правила сайта</a>
            </div>
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
        @else
            <a href="#" class="nav-item">
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
        <button class="nav-item button-without-styles-all" id="hamburgerBtn">
            <svg class="nav-icon"><use href="#stack-2"></use></svg>
            <h2 class="nav-label">Меню</h2>
        </button>
    </navbar>
