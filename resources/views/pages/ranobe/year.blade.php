@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/anime/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/rating.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown-menu.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/components/dropdown-menu.js') }}"></script>
@endpush
@section('title', "Читать «Класс превосходства» | {$year} год | DCOTE")
@section('description', "Список всех томов {$year} года новеллы «Добро пожаловать в класс превосходства». Выбирайте год и приступайте к чтению с высоким качеством перевода на DCOTE.")
@section('content')
<svg style="display: none;">
    <symbol id="star" viewBox="0 0 36 35">
        <path d="M11.8485 10.5645L1.39716 12.1147L1.21697 12.148C0.348755 12.3814 -0.175449 13.2982 0.05389 14.1817C0.135797 14.4818 0.283229 14.7318 0.496187 14.9485L8.06438 22.4497L6.27881 33.0348V33.2182C6.1969 34.135 6.88492 34.9185 7.80227 34.9851C8.09714 35.0018 8.392 34.9351 8.65411 34.8018L18.0079 29.801L27.3289 34.8018L27.4927 34.8851C28.3281 35.2185 29.2782 34.8018 29.6223 33.9516C29.7369 33.6683 29.7697 33.3515 29.7205 33.0515L27.935 22.4664L35.5032 14.9652L35.6342 14.8152C36.2076 14.0984 36.0929 13.0482 35.3885 12.4648C35.1592 12.2814 34.8807 12.148 34.5858 12.1147L24.1345 10.5645L19.4494 0.929533C19.0563 0.0960627 18.0734 -0.237325 17.2543 0.17941C16.9267 0.346104 16.6646 0.612815 16.5172 0.929533L11.8485 10.5645Z" fill="currentColor"/>
    </symbol>
</svg>
<div class="navigation-links">
    <a href="{{ route('home') }}"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a href="{{ route('ranobe.index') }}"><span>РАНОБЭ</span></a>
    <p>/</p>
    <a href="{{ route('ranobe.year',['year'=>$year]) }}"><span>{{ $year }} ГОД</span></a>
</div>
    @foreach ($volumes as $index => $volume)
        @php
            $volume_number_rounded = floatval($volume->volume_number);
            $total = (int)$volume->all_chapters;
            $released = (int)$volume->chapters()->count() ?? 0;
            $percent = ($total > 0) ? min(100, round(($released / $total) * 100, 2)) : 0;
        @endphp
        <div class="cont scale-in" data-volume="{{ (int)$volume_number_rounded }}">
            <div class="image-wrapper">
                <picture>
                    <source media="(max-width: 768px)" 
                            srcset="{{ Storage::url($volume->cover_image_mobile) }}" 
                            type="image/webp">
                    <img src="{{ Storage::url($volume->cover_image) }}" 
                        @if($index < 2) fetchpriority="high" @else loading="lazy" @endif 
                        decoding="async" 
                        alt="Обложка {{$volume_number_rounded}} тома {{ $year }} года">
                </picture>
            </div>
            <div class="desc slide-in-left">
                <div class="head">
                    <h1>{{ $volume_number_rounded }} ТОМ</h1>
                    <div class="rating">
                        <div class="star-and-number visual"><svg style="color:#ffb147" class="star-icon">
                                <use href="#star"></use>
                            </svg>
                            <h3>9</h3>
                        </div>
                        <p>Всего оценок: 150</p>
                    </div>
                </div>
                <dl class="info-block">
                    <dt>Статус издания:</dt>
                    <dd class="{{ $volume->color }}">{{ $volume->status }}</dd>
                    <dt>Общая нумерация:</dt>
                    <dd>{{ $volume->general_number }}</dd>
                </dl>
                <dl class="info-block">
                    <dt>Дата выхода (книга):</dt>
                    <dd>{{ russian_date($volume->release_date_book) }}</dd>
                    <dt>Дата выхода (цифра):</dt>
                    <dd>{{ russian_date($volume->release_date_digital) }}</dd>
                </dl>
                <dl class="info-block">
                    <dt>Объём тома:</dt>
                    <dd>{{ $volume->all_chapters }}</dd>
                    <dt>ISBN книги:</dt>
                    <dd>{{ $volume->isbn}}</dd>
                </dl>
                <div class="mobile-info">
                    <button class="dropdown-menu-btn no-glow" aria-expanded="false" data-target="menu-{{ $index }}">БОЛЬШЕ ИНФОРМАЦИИ
                        <svg class="dropdown-icon">
                        <use href="#dropdown"></use>
                        </svg>
                    </button>
                    <div class="dropdown-wrapper" id="menu-{{ $index }}">
                        <div class="dropdown-content">
                            <dl class="info-block">
                                <dt>Статус издания:</dt>
                                <dd class="{{ $volume->color }}">{{ $volume->status }}</dd>
                                <dt>Общая нумерация:</dt>
                                <dd>{{ $volume->general_number }}</dd>
                                <dt>Дата выхода (книга):</dt>
                                <dd>{{ russian_date($volume->release_date_book) }}</dd>
                                <dt>Дата выхода (цифра):</dt>
                                <dd>{{ russian_date($volume->release_date_digital) }}</dd>
                                <dt>Объём тома:</dt>
                                <dd>{{ $total }}</dd>
                                <dt>ISBN книги:</dt>
                                <dd>{{ $volume->isbn}}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <p><b>Переведено:</b> {{ $released }} из {{ $total }} глав</p>
                <div class="progress-bar" style="--progress-width: {{ $percent }}%"></div>
                <a class="link-like-button" href='{{ route('ranobe.volume',['year'=>$year,'volume'=>$volume_number_rounded]) }}'>СТРАНИЦА ТОМА</a>
                <div class="button-line">
                    <button class="dropdown-btn no-glow" disabled aria-expanded="false">ДОБАВИТЬ В
                        <svg class="dropdown-icon">
                        <use href="#dropdown"></use>
                        </svg>
                    </button>
                    <a href="{{route('ranobe.chapter', ['year'=>$year,'volume'=>$volume_number_rounded,'chapter'=>1]) }}" class="link-like-button no-glow">НАЧАТЬ ЧИТАТЬ</a>
                </div>
                <div class="button-line mobile">
                    <button class="dropdown-btn no-glow" disabled aria-expanded="false">ДОБАВИТЬ В
                        <svg class="dropdown-icon">
                        <use href="#dropdown"></use>
                        </svg>
                    </button>
                </div>
                <div class="button-line mobile">
                        <a href="{{route('ranobe.chapter', ['year'=>$year,'volume'=>$volume_number_rounded,'chapter'=>1]) }}" class="link-like-button no-glow">НАЧАТЬ ЧИТАТЬ</a>
                </div>
            </div>
        </div>
    @endforeach
    <ul class="dropdown-list hidden">
        <li>
            <p>Смотрю</p>
        </li>
        <li>
            <p>Брошеное</p>
        </li>
        <li>
            <p>Любимое</p>
        </li>
    </ul>
    @endsection
