@extends('layouts.app')
@push ('scripts-early')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endpush
@push('styles')
    @vite('resources/css/pages/login-reg.css')
@endpush
@push('scripts')
    @vite('resources/js/pages/reg-login.js')
@endpush
@section('title', 'DCOTE | Авторизация')
@section('content')
<svg style="display: none;">
    <symbol id="info-circle" viewBox="0 0 24 24">
        <path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm0 9h-1l-.117 .007a1 1 0 0 0 0 1.986l.117 .007v3l.007 .117a1 1 0 0 0 .876 .876l.117 .007h1l.117 -.007a1 1 0 0 0 .876 -.876l.007 -.117l-.007 -.117a1 1 0 0 0 -.764 -.857l-.112 -.02l-.117 -.006v-3l-.007 -.117a1 1 0 0 0 -.876 -.876l-.117 -.007zm.01 -3l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007z" />
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
                <div class="password-title">
                    <label for="password">
                        <h3>Пароль</h3>
                    </label>
                </div>
                <div class="input-with-icon">
                    <input type="password"
                           id="password"
                           name="password"
                           autocomplete="password"
                           title="Не менее 8 и не более 72 символов"
                           required>
                    @include('partials.password-toggle')
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
