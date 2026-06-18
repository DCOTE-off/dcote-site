@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/anime/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/rating.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/components/rating.js') }}"></script>
    <script src="{{ asset('js/components/dropdown-menu.js') }}"></script>
@endpush
@section('title', "Читать «Класс превосходства» | {$year} год | DCOTE")
@section('description', "Список всех томов {$year} года новеллы «Добро пожаловать в класс превосходства». Выбирайте год и приступайте к чтению с высоким качеством перевода на DCOTE.")
@section('content')
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
                        @include('partials.rating', [
                            'rateableType' => 'ranobe_volume',
                            'rateableId' => $volume->id,
                            'userRating' => $volume->volume_user_rating ?? 0,
                            'avgRating' => round((float) ($volume->volume_avg_rating ?? 0), 1),
                            'ratingsCount' => $volume->volume_ratings_count ?? 0,
                        ])
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
