@extends('layouts.app')
@push ('scripts-early')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endpush
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/login-reg.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/pages/reg-login.js') }}"></script>
@endpush
@section('title', 'DCOTE | Авторизация')
@section('content')
<svg style="display: none;">
    <symbol id="info-circle" viewBox="0 0 24 24">
        <path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm0 9h-1l-.117 .007a1 1 0 0 0 0 1.986l.117 .007v3l.007 .117a1 1 0 0 0 .876 .876l.117 .007h1l.117 -.007a1 1 0 0 0 .876 -.876l.007 -.117l-.007 -.117a1 1 0 0 0 -.764 -.857l-.112 -.02l-.117 -.006v-3l-.007 -.117a1 1 0 0 0 -.876 -.876l-.117 -.007zm.01 -3l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007z" />
    </symbol>
    <symbol id="eye" viewBox="0 0 17 12">
        <path d="M8.49807 0C11.8108 0 14.5676 1.82364 16.7375 5.36585L16.9073 5.65103L16.9459 5.72608L16.9691 5.78612V5.83114L16.9923 5.89118V5.96623L17 6.04878V6.13133C17 6.13133 16.9768 6.18386 16.9691 6.21388L16.9382 6.29644L16.9073 6.34897L16.8919 6.37148C14.7606 10.0038 12.027 11.9099 8.73745 12H8.49807C5.10039 12 2.28958 10.0863 0.104247 6.37148C-0.034749 6.13884 -0.034749 5.86116 0.104247 5.62852C2.28958 1.9137 5.10039 0 8.49807 0ZM8.49807 3.75235C7.21622 3.75235 6.18147 4.75797 6.18147 6.00375C6.18147 7.24953 7.21622 8.25516 8.49807 8.25516C9.77992 8.25516 10.8147 7.24953 10.8147 6.00375C10.8147 4.75797 9.77992 3.75235 8.49807 3.75235Z" fill="white" fill-opacity="0.5"/>
    </symbol>
    <symbol id="eye-off" viewBox="0 0 17 17">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.267408 1.56523C-0.0891359 1.20868 -0.0891359 0.623951 0.267408 0.267408C0.623951 -0.0891359 1.20868 -0.0891359 1.56523 0.267408L4.78125 3.48343C5.89367 2.87018 7.15583 2.44232 8.50357 2.44232C10.379 2.44232 12.0761 3.2695 13.4381 4.25357C14.8073 5.23763 15.884 6.41422 16.5401 7.20575C16.8324 7.57655 16.9893 8.04006 16.9893 8.50357C16.9893 8.96707 16.8324 9.43058 16.5401 9.79425C15.9411 10.5216 14.9927 11.5627 13.7875 12.4897L16.7326 15.4348C17.0891 15.7913 17.0891 16.376 16.7326 16.7326C16.376 17.0891 15.7913 17.0891 15.4348 16.7326L0.267408 1.56523ZM10.9708 9.67303C11.142 9.31648 11.2347 8.92429 11.2347 8.50357C11.2347 6.99895 10.0153 5.77244 8.50357 5.77244C8.08284 5.77244 7.69065 5.86514 7.3341 6.03628L10.9708 9.67303ZM0.467072 7.20575C0.85927 6.73511 1.40122 6.12185 2.06439 5.50147L10.7498 14.1869C10.0367 14.4293 9.28796 14.5719 8.50357 14.5719C6.62815 14.5719 4.931 13.7448 3.569 12.7607C2.19987 11.7766 1.12311 10.6 0.467072 9.80851C0.174706 9.43771 0.0178272 8.9742 0.0178272 8.5107C0.0178272 8.04719 0.174706 7.58368 0.467072 7.22001V7.20575Z" fill="white" fill-opacity="0.5"/>
    </symbol>
</svg>
<div class="navigation-links">
    <a href="{{ route('home') }}"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a class="current-page" href="{{ route('login') }}"><span>АВТОРИЗАЦИЯ</span></a>
</div>
<div class="auth-container auth-container-login">
    <div class="auth-image scale-in">
        <picture>
            <source media="(max-width: 768px)" srcset="/images/auth/auth_4-mobile.webp" type="image/webp">
            <img src="/images/auth/auth_4.webp" alt="Изображение в регистрации">
        </picture>
    </div>
    <div class="auth-form scale-in slide-in-left">
        <form method="post" id="registration-form" novalidate>
            @csrf
            <h1>АВТОРИЗАЦИЯ</h1>
            <div class="input-group">
                <label for="tag">
                    <h3>Имя пользователя</h3>
                </label>
                <input id="tag" spellcheck="false" value="{{ old('tag') }}"
                name="tag" required
                autocomplete="username">
                <span class="error-bubble" id="tag-error"></span>
                <p>Забыл(а) имя пользователя? <a href="">Восстановить</a></p>
            </div>
            <div class="input-group">
                <div class="password-title"><label for="password">
                        <h3>Пароль</h3>
                    </label>
                </div>
                <div class="input-with-icon">
                    <input type="password" id="password" name="password"
                    autocomplete="password"
                    title="Не менее 8 и не более 72 символов" required>
                    <button type="button" class="password-toggle button-without-styles-all" aria-label="Показать пароль">
                        <svg class="eye-icon">
                            <use href="#eye"></use>
                        </svg>
                        <svg class="eye-off-icon" style="display: none;">
                            <use href="#eye-off"></use>
                        </svg>
                    </button>
                </div>
                <span class="error-bubble" id="password-error"></span>
                <p>Забыл(а) пароль? <a href="">Восстановить</a></p>
            </div>
            <div class="turnstile-slot">
                <div class="cf-turnstile"
                    data-sitekey="{{ config('services.cloudflare.site_key') }}"
                    data-callback="onCaptchaSuccess">
                </div>
            </div>
            <div class="input-group">
                <button type="submit" class="submit-btn" disabled>АВТОРИЗОВАТЬСЯ</button>
                <p>Нет аккаунта? <a href="{{ route('register') }}"> Зарегистрируйся</a></p>
            </div>
        </form>
    </div>
</div>
    @endsection
