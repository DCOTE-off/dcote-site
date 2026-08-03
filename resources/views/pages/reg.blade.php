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
@section('title', 'DCOTE | Регистрация')
@section('content')
<svg style="display: none;">
    <symbol id="info-circle" viewBox="0 0 24 24">
        <path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm0 9h-1l-.117 .007a1 1 0 0 0 0 1.986l.117 .007v3l.007 .117a1 1 0 0 0 .876 .876l.117 .007h1l.117 -.007a1 1 0 0 0 .876 -.876l.007 -.117l-.007 -.117a1 1 0 0 0 -.764 -.857l-.112 -.02l-.117 -.006v-3l-.007 -.117a1 1 0 0 0 -.876 -.876l-.117 -.007zm.01 -3l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007z" />
    </symbol>
</svg>
<div class="navigation-links">
    <a href="{{ route('home') }}"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a class="current-page" href="{{ route('register') }}"><span>РЕГИСТРАЦИЯ</span></a>
</div>
<div class="auth-container">
    <div class="auth-image scale-in">
        <picture>
            <source media="(max-width: 768px)" srcset="/images/auth/auth_4-mobile.webp" type="image/webp">
            <img src="/images/auth/auth_4.webp" alt="Изображение в регистрации">
        </picture>
    </div>
    <div class="auth-form scale-in slide-in-left">
        <form method="POST" id="registration-form" novalidate>
            @csrf
            <h1>РЕГИСТРАЦИЯ</h1>
            <div class="input-group">
                <label for="tag">
                    <h3>Имя пользователя</h3>
                </label>
                <input id="tag"
                       name="tag"
                       value="{{ old('tag') }}"
                       placeholder="Логин для возможности входа в аккаунт"
                       autocomplete="username"
                       spellcheck="false"
                       pattern="^[a-z0-9_]{5,32}$"
                       title="От 5 до 32 символов: только маленькая латиница, цифры и '_'"
                       required>
                <span class="error-bubble" id="tag-error" style="{{ $errors->has('tag') ? 'display: flex;' : '' }}">
                    {{ $errors->first('tag') }}
                </span>
            </div>
            <div class="input-group">
                <label for="nickname">
                    <h3>Отображаемое имя</h3>
                </label>
                <input id="nickname"
                       name="nickname"
                       value="{{ old('nickname') }}"
                       placeholder="Никнейм, отображаемый в профиле"
                       spellcheck="false"
                       pattern=".{1,64}$"
                       title="Не менее 1 и не более 64 символов"
                       required>
                <span class="error-bubble" id="nickname-error" style="{{ $errors->has('nickname') ? 'display: flex;' : '' }}">
                    {{ $errors->first('nickname') }}
                </span>
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
                           placeholder="Не менее 8 символов"
                           autocomplete="new-password"
                           pattern=".{8,72}$"
                           title="Не менее 8 и не более 72 символов"
                           required>
                    @include('partials.password-toggle')
                </div>
                <span class="error-bubble" id="password-error" style="{{ $errors->has('password') ? 'display: flex;' : '' }}">
                    {{ $errors->first('password') }}
                </span>
            </div>
            <div class="input-group">
                <div class="password-title">
                    <label for="password_confirmation">
                        <h3>Повторный ввод пароля</h3>
                    </label>
                </div>
                <div class="input-with-icon">
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           placeholder="Повторите пароль с поля выше"
                           autocomplete="new-password"
                           required>
                    @include('partials.password-toggle')
                </div>
                <span class="error-bubble" id="password_confirmation-error" style="{{ $errors->has('password') ? 'display: flex;' : '' }}">
                    {{ $errors->first('password') }}
                </span>
            </div>
            <div class="check" style="text-align: center;">
                <p>Регистрируясь, вы принимаете <a href="{{ route('rules') }}">правила сайта</a> и <a href="{{ route('privacy_policy') }}">политику конфиденциальности</a></p>
            </div>
            <div class="turnstile-slot">
                <div class="cf-turnstile"
                    data-sitekey="{{ config('services.cloudflare.site_key') }}"
                    data-callback="onCaptchaSuccess">
                </div>
            </div>
            <div class="input-group">
                <button type="submit" class="submit-btn" disabled>ЗАРЕГИСТРИРОВАТЬСЯ</button>
            </div>
        </form>
    </div>
</div>
    @endsection
