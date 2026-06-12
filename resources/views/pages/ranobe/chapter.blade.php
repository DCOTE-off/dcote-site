@extends('layouts.app') 
@push('scripts-early')
<script>
(function() {
    try {
        var saved = window.localStorage.getItem('dcote-reading-settings');
        if (saved) {
            var s = JSON.parse(saved);
            var r = document.documentElement;
            if (s.fontSize) r.style.setProperty('--font-size-baze', s.fontSize + 'px');
            if (s.lineHeight) r.style.setProperty('--text-line-height', s.lineHeight);
            if (s.paragraphGap) r.style.setProperty('--block-padding', (s.paragraphGap / 2) + 'px');
            if (s.indent !== undefined) r.style.setProperty('--text-intend', s.indent ? '0.875em' : '0');
            if (s.navigation !== undefined) r.style.setProperty('--navigation-display', s.navigation ? 'flex' : 'none');
            if (s.contWidth) r.style.setProperty('--cont-width', s.contWidth + '%');
            if (s.fontFamily) {
                var fontCSS = {'Vag Rounded Next':"'Vag Rounded Next', sans-serif",'Times New Roman':"'Times New Roman', Georgia, serif",'Open Sans':"'Open Sans', sans-serif"}[s.fontFamily];
                if (fontCSS) r.style.setProperty('--font-family', fontCSS);
            }
            if (s.theme) {
                var themeMap = {'Стандартная':{text:'#e9e9e9',bg:'rgb(7, 18, 32)'},'Тёмная':{text:'#dddddd',bg:'#141414'},'Серая':{text:'#dbdbdb',bg:'#434751'},'Светлая':{text:'#212529',bg:'#f2f2f3'},'Книжная':{text:'#262425',bg:'#e5cf9d'}}[s.theme];
                if (themeMap) {
                    r.style.setProperty('--primary-text-color', themeMap.text);
                    r.style.setProperty('--body-bg-color', themeMap.bg);
                }
            }
        }
        if (window.localStorage.getItem('dcote-nav-hidden') === 'true') {
            document.documentElement.classList.add('nav-hidden');
        }
    } catch(e) {}
})();
</script>
@endpush
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/ranobe/chapter.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/rating.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown-select.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/reading-settings.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/components/dropdown-select.js') }}"></script>
    <script src="{{ asset('js/pages/reading-settings.js') }}"></script>
@endpush
@section('title', "Читать «Класс превосходства» | {$year} год {$volume_number_rounded} том {$chapter} глава | DCOTE")
@section('description', "Читать {$chapterModel->title} {$volume_number_rounded} тома новеллы «Добро пожаловать в класс превосходства». Читайте с высоким качеством перевода на DCOTE.")
@section('content')
<div class="navigation-links">
    <a href="{{ route('home') }}"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a href="{{ route('ranobe.index') }}"><span>РАНОБЭ</span></a>
    <p>/</p>
    <a href="{{ route('ranobe.year',['year'=>$year]) }}"><span>{{ $year }} ГОД</span></a>
    <p>/</p>
    <a href="{{ route('ranobe.volume',['year'=>$year,'volume'=>$volume_number_rounded]) }}"><span>{{ $volume_number_rounded }} ТОМ</span></a>
    <p>/</p>
    <a href="{{ route('ranobe.chapter',['year'=>$year,'volume'=>$volume_number_rounded,'chapter'=>$chapter]) }}"><span>{{ $chapter }} ГЛАВА</span></a>
</div>
<div class="chapter-container">
    <h3 class="main-title">{!! strip_tags(Illuminate\Support\Str::markdown($chapterModel->title)) !!}</h3>
    <article class="chapter-content">
        {!! $htmlContent !!}
    </article>
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
        <div class="text-and-checkbox">
            <p>Навигация сайта:</p>
            <div class="settings-btn-cont">
                <label class="settings-btn" for="navigationCheckbox">
                    <input type="checkbox" id="navigationCheckbox" class="settings-switch" aria-label="Навигация сайта">
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
        <div class="dd-buttons">
            <div class="dropdown-select-wrapper">
                <button class="dropdown-select-btn no-glow" data-target="fontDdContent" aria-expanded="false">ШРИФТ
                    <svg class="dropdown-icon">
                    <use href="#dropdown"></use>
                    </svg>
                </button>
                <div class="dropdown-content" id="fontDdContent">
                    <button class="dropdown-list-value button-without-styles-all">
                        Vag Rounded Next
                        <span class="check-mark-bg"><svg class="check-mark-icon"><use href="#check-mark"></use></svg></span>
                    </button>
                    <button class="dropdown-list-value button-without-styles-all">
                        Times New Roman
                        <span class="check-mark-bg"><svg class="check-mark-icon"><use href="#check-mark"></use></svg></span>
                    </button>
                    <button class="dropdown-list-value button-without-styles-all">
                        Open Sans
                        <span class="check-mark-bg"><svg class="check-mark-icon"><use href="#check-mark"></use></svg></span>
                    </button>
                </div>
            </div>
            <div class="dropdown-select-wrapper">
                <button class="dropdown-select-btn no-glow" data-target="themeDdContent" aria-expanded="false">ТЕМА
                    <svg class="dropdown-icon">
                    <use href="#dropdown"></use>
                    </svg>
                </button>
                <div class="dropdown-content" id="themeDdContent">
                    <button class="dropdown-list-value button-without-styles-all" id="standart-theme">
                        Стандартная
                        <span class="check-mark-bg"><svg class="check-mark-icon"><use href="#check-mark"></use></svg></span>
                    </button>
                    <button class="dropdown-list-value button-without-styles-all" id="dark-theme">
                        Тёмная
                        <span class="check-mark-bg"><svg class="check-mark-icon"><use href="#check-mark"></use></svg></span>
                    </button>
                    <button class="dropdown-list-value button-without-styles-all" id="grey-theme">
                        Серая
                        <span class="check-mark-bg"><svg class="check-mark-icon"><use href="#check-mark"></use></svg></span>
                    </button>
                    <button class="dropdown-list-value button-without-styles-all" id="light-theme">
                        Светлая
                        <span class="check-mark-bg"><svg class="check-mark-icon"><use href="#check-mark"></use></svg></span>
                    </button>
                    <button class="dropdown-list-value button-without-styles-all" id="book-theme">
                        Книжная
                        <span class="check-mark-bg"><svg class="check-mark-icon"><use href="#check-mark"></use></svg></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="read-nav">
        <a class="read-item {{ !$prev_link ? 'disabled_a' : '' }}"  href="{{ $prev_link }}">
            <svg class="arrow" viewBox="0 0 12 8"><use href="#mini-arrow-left"></use></svg>
        </a>
        <div class="center-items">
            <a class="read-item" href="{{ route('ranobe.volume',['year'=>$year,'volume'=>$volume_number_rounded]) }}">
                <svg class="nav-icon" viewBox="0 0 17 15"><use href="#list-details"></use></svg>
            </a>
            <button class="read-item button-without-styles-all">
                <svg class="nav-icon" viewBox="0 0 11 15"><use href="#mark"></use></svg>
            </button>
            <button class="read-item button-without-styles-all" id="settingsReadBtn">
                <svg class="nav-icon" viewBox="0 0 25 25"><use href="#settings"></use></svg>
            </button>
        </div>
        <a class="read-item {{ !$next_link ? 'disabled_a' : '' }}" href="{{ $next_link }}">
            <svg class="arrow" viewBox="0 0 12 8"><use href="#mini-arrow-right"></use></svg>
        </a>
    </div>
    <div class="chapters-controls">
        <a class="link-like-button {{ !$prev_link ? 'disabled_a' : '' }}"  href="{{ $prev_link }}" class="prev-episode-btn">ПРЕДЫДУЩАЯ ГЛАВА</a>
        <a class="link-like-button" href="{{ route('ranobe.volume',['year'=>$year,'volume'=>$volume_number_rounded]) }}">ВСЕ ГЛАВЫ</a>
        <a class="link-like-button {{ !$next_link ? 'disabled_a' : '' }}" href="{{ $next_link }}" class="next-episode-btn">СЛЕДУЮЩАЯ ГЛАВА</a>
    </div>
    <div class="chapters-controls mobile">
        <a class="link-like-button {{ !$prev_link ? 'disabled_a' : '' }}"  href="{{ $prev_link }}" class="prev-episode-btn">
            <svg class="slider-icon" width="30" height="30">
                <use href="#arrow-left"></use>
            </svg>
        </a>
        <a class="link-like-button" href="{{ route('ranobe.volume',['year'=>$year,'volume'=>$volume_number_rounded]) }}">
            <svg class="slider-icon" width="30" height="30">
                <use href="#list-details"></use>
            </svg>
        </a>
        <a class="link-like-button {{ !$next_link ? 'disabled_a' : '' }}"  href="{{ $next_link }}" class="next-episode-btn">
            <svg class="slider-icon" width="30" height="30">
                <use href="#arrow-right"></use>
            </svg>
        </a>
    </div>
</div>
    @endsection
