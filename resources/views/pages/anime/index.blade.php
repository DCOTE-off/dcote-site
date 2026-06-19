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
@section('title', 'Смотреть аниме «Класс превосходства» | Все сезоны | DCOTE')
@section('description', 'Список всех сезонов и серий аниме «Добро пожаловать в класс превосходства». Выбирайте сезон и приступайте к просмотру в высоком качестве на DCOTE.')
@section('content')
<div class="navigation-links">
    <a href="{{ route('home') }}"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a class="current-page" href="{{ route('anime.index') }}"><span>АНИМЕ</span></a>
</div>
    @foreach ($seasons_list as $index => $season)
        @php
            $total = (int)$season->number_of_episodes;
            $released = (int)$season_realesed[$index]->episode_count;
            $percent = ($total > 0) ? min(100, round(($released / $total) * 100, 2)) : 0;
        @endphp
        <div class="cont scale-in" data-season="{{ (int)$season->season_number }}">
            <div class="image-wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/anime/anime-banner-season-{{ $season->id }}-mobile.webp" type="image/webp">
                    <img src="/images/anime/anime-banner-season-{{ $season->id }}.webp" @if($index < 2) fetchpriority="high" @else loading="lazy" @endif decoding="async" alt="Обложка сезона">
                </picture>
            </div>
            <div class="desc slide-in-left">
                <div class="head">
                    <h1>{{ $season->id }} СЕЗОН</h1>
                    @include('partials.rating', [
                        'rateableType' => 'anime_episode',
                        'rateableId' => 0,
                        'userRating' => 0,
                        'avgRating' => round((float) ($season->season_avg_rating ?? 0), 1),
                        'ratingsCount' => $season->season_ratings_count ?? 0,
                        'visualOnly' => true,
                    ])
                </div>
                <dl class="info-block">
                    <dt>Статус сериала:</dt>
                    <dd class="{{ $season->color }}">{{ $season->status }}</dd>
                    <dt>Сезон:</dt>
                    <dd>{{ $season->season_time }}</dd>
                </dl>
                <dl class="info-block">
                    <dt>День релиза:</dt>
                    <dd>{{ $season->release_time }}</dd>
                    <dt>Студия:</dt>
                    <dd>{{ $season->studio }}</dd>
                </dl>
                <dl class="info-block">
                    <dt>Кол-во серий:</dt>
                    <dd>{{ $season->number_of_episodes }}</dd>
                    <dt>Экранизируемые тома:</dt>
                    <dd>{{ $season->adapt_volumes }} {{ $season->adapt_volumes_brackets }}</dd>
                </dl>
                <div class="mobile-info">
                    <button class="dropdown-menu-btn no-glow" aria-expanded="false" data-target="menu-{{ $index }}">Больше информации
                        <svg class="dropdown-icon">
                        <use href="#dropdown"></use>
                        </svg>
                    </button>
                    <div class="dropdown-wrapper" id="menu-{{ $index }}">
                        <div class="dropdown-content">
                            <dl class="info-block">
                                <dt>Статус сериала:</dt>
                                <dd class="{{ $season->color }}">{{ $season->status }}</dd>
                                <dt>Сезон:</dt>
                                <dd>{{ $season->season_time }}</dd>
                                <dt>День релиза:</dt>
                                <dd>{{ $season->release_time }}</dd>
                                <dt>Студия:</dt>
                                <dd>{{ $season->studio }}</dd>
                                <dt>Кол-во серий:</dt>
                                <dd>{{ $season->number_of_episodes }}</dd>
                                <dt>Экранизация:</dt>
                                <dd>{{ $season->adapt_volumes }} {{ $season->adapt_volumes_brackets }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="progress-info">
                    <p><b>Выпущено:</b> {{ $season_realesed[$index]->episode_count }} из {{ $season->number_of_episodes }} серий</p>
                    <div class="progress-bar" style="--progress-width: {{ $percent }}%"></div>
                </div>
                <a class="link-like-button" href='{{ route('anime.season',['season'=> (int)$season->season_number]) }}'>СТРАНИЦА СЕЗОНА</a>
                <div class="button-line">
                    <button class="dropdown-btn no-glow" disabled aria-expanded="false">ДОБАВИТЬ В
                        <svg class="dropdown-icon">
                        <use href="#dropdown"></use>
                        </svg>
                    </button>
                    <a href="{{route('anime.episode', ['season'=>(int)$season->season_number,'episode'=>1]) }}" class="link-like-button no-glow">НАЧАТЬ СМОТРЕТЬ</a>
                </div>
                <div class="button-line mobile">
                    <button class="dropdown-btn no-glow" disabled aria-expanded="false">ДОБАВИТЬ В
                        <svg class="dropdown-icon">
                        <use href="#dropdown"></use>
                        </svg>
                    </button>
                </div>
                <div class="button-line mobile">
                        <a href="{{route('anime.episode', ['season'=>(int)$season->season_number,'episode'=>1]) }}" class="link-like-button no-glow">НАЧАТЬ СМОТРЕТЬ</a>
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
