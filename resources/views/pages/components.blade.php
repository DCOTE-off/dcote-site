@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/rules.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/reading-settings.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/pages/reading-settings.js') }}"></script>
@endpush
@section('title', 'DCOTE | Политика конфиденциальности')
@section('description', 'Политика конфиденциальности сайта DCOTE')
@section('content')
    <div class="read-settings">
        <div class="text-and-checkbox">
            <p>Отступ:</p>
            <div class="settings-btn-cont">
                <label class="settings-btn" for="indentCheckbox">
                    <input type="checkbox" id="indentCheckbox" class="settings-switch" aria-label="Отступ">
                    <span class="settings-btn__track"></span>
                </label>
            </div>
        </div>
        <div class="text-and-checkbox">
            <p>Изображения:</p>
            <div class="settings-btn-cont">
                <label class="settings-btn" for="imagesCheckbox">
                    <input type="checkbox" id="imagesCheckbox" class="settings-switch" aria-label="Изображения">
                    <span class="settings-btn__track"></span>
                </label>
            </div>
        </div>
        <div class="text-and-checkbox">
            <p>Название главы:</p>
            <div class="settings-btn-cont">
                <label class="settings-btn" for="titleCheckbox">
                    <input type="checkbox" id="titleCheckbox" class="settings-switch" aria-label="Название главы">
                    <span class="settings-btn__track"></span>
                </label>
            </div>
        </div>
        <div class="label-and-range">
            <label for="fontSize"><p>Размер шрифта:</p> <strong><p id="fontSizeValue">16</p><p>px</p></strong></label>
            <input type="range" id="fontSize" min="8" max="40" value="16" step="1">
        </div>
        <div class="label-and-range">
            <label for="lineHeight"><p>Высота строк:</p> <strong><p id="lineHeightValue">1.6</p></strong></label>
            <input type="range" id="lineHeight" min="1" max="2.4" value="1.6" step="0.1">
        </div>
        <div class="label-and-range">
            <label for="paragraphGap"><p>Отступ между абзацами:</p> <strong><p id="paragraphGapValue">10</p><p>px</p></strong></label>
            <input type="range" id="paragraphGap" min="5" max="45" value="10" step="1">
        </div>
        <div class="label-and-range">
            <label for="contWidth"><p>Ширина контейнера:</p> <strong><p id="contWidthValue">80</p><p>%</p></strong></label>
            <input type="range" id="contWidth" min="1" max="100" value="80" step="1">
        </div>
    </div>
    <div class="read-nav">
        <a class="read-item">
            <svg class="arrow" viewBox="0 0 12 8"><use href="#mini-arrow-left"></use></svg>
        </a>
        <div class="center-items">
            <a class="read-item">
                <svg class="nav-icon" viewBox="0 0 17 15"><use href="#list-details"></use></svg>
            </a>
            <button class="read-item button-without-styles-all">
                <svg class="nav-icon" viewBox="0 0 11 15"><use href="#mark"></use></svg>
            </button>
            <button class="read-item button-without-styles-all" id="settingsReadBtn">
                <svg class="nav-icon" viewBox="0 0 25 25"><use href="#settings"></use></svg>
            </button>
        </div>
        <a class="read-item">
            <svg class="arrow" viewBox="0 0 12 8"><use href="#mini-arrow-right"></use></svg>
        </a>
    </div>
@endsection