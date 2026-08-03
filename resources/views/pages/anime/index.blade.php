@extends('layouts.app') 
@push('styles')
    @vite(['resources/css/pages/anime/index.css', 'resources/css/components/dropdown.css', 'resources/css/components/dropdown-menu.css', 'resources/css/components/rating.css'])
@endpush
@push('scripts')
    @vite(['resources/js/components/rating.js', 'resources/js/components/dropdown-menu.js', 'resources/js/pages/selection-cards.js'])
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
            $seasonNumber = (int) $season->season_number;
            $total = (int) $season->number_of_episodes;
            $released = (int) $season->released_episodes_count;
            $percent = ($total > 0) ? min(100, round(($released / $total) * 100, 2)) : 0;
            $isAnnounced = $season->isAnnounced();
            $mobileCover = "/images/anime/anime-banner-season-{$seasonNumber}-mobile.webp";
            $desktopCover = $season->img_src ?: "/images/anime/anime-banner-season-{$seasonNumber}.webp";
            $seasonUrl = route('anime.season', ['season' => $seasonNumber]);
            $firstEpisodeUrl = route('anime.episode', ['season' => $seasonNumber, 'episode' => 1]);
        @endphp
        <div class="cont selection-card anime-season-card scale-in" data-season="{{ $seasonNumber }}">
            <div class="image-wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="{{ $mobileCover }}" type="image/webp">
                    <img
                        src="{{ $desktopCover }}"
                        @if($index < 2) fetchpriority="high" @else loading="lazy" @endif
                        decoding="async"
                        alt="Обложка сезона"
                    >
                </picture>
            </div>
            <div class="desc slide-in-left">
                <div class="head">
                    <h1>{{ $seasonNumber }} СЕЗОН</h1>
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
                    <p><b>Выпущено:</b> {{ $released }} из {{ $season->number_of_episodes }} серий</p>
                    <div class="progress-bar" style="--progress-width: {{ $percent }}%"></div>
                </div>
                @if ($isAnnounced)
                    <button type="button" class="link-like-button" disabled>СТРАНИЦА СЕЗОНА</button>
                @else
                    <a class="link-like-button" href="{{ $seasonUrl }}">СТРАНИЦА СЕЗОНА</a>
                @endif
                <div class="button-line">
                    <button class="dropdown-btn no-glow" disabled aria-expanded="false">ДОБАВИТЬ В
                        <svg class="dropdown-icon">
                        <use href="#dropdown"></use>
                        </svg>
                    </button>
                    @if ($isAnnounced)
                        <button type="button" class="link-like-button no-glow" disabled>НАЧАТЬ СМОТРЕТЬ</button>
                    @else
                        <a href="{{ $firstEpisodeUrl }}" class="link-like-button no-glow">НАЧАТЬ СМОТРЕТЬ</a>
                    @endif
                </div>
                <div class="button-line mobile">
                    <button class="dropdown-btn no-glow" disabled aria-expanded="false">ДОБАВИТЬ В
                        <svg class="dropdown-icon">
                        <use href="#dropdown"></use>
                        </svg>
                    </button>
                </div>
                <div class="button-line mobile">
                    @if ($isAnnounced)
                        <button type="button" class="link-like-button no-glow" disabled>НАЧАТЬ СМОТРЕТЬ</button>
                    @else
                        <a href="{{ $firstEpisodeUrl }}" class="link-like-button no-glow">НАЧАТЬ СМОТРЕТЬ</a>
                    @endif
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
