@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/anime/season.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/rating.css') }}">

@endpush
@push('scripts')
    <script src="{{ asset('js/components/rating.js') }}"></script>
    <script src="{{ asset('js/pages/anime/season.js') }}"></script>
    <script src="{{ asset('js/pages/cover-description-cards.js') }}"></script>
@endpush
@section('title', "Аниме «Класс превосходства» {$season} сезон | Список серий | DCOTE")
@section('description', "Смотреть {$season} сезон «Добро пожаловать в класс превосходства» онлайн. Описание сезона, список серий и даты выхода на сайте DCOTE.")
@section('content')
<div class="navigation-links">
    <a href="{{ route('home') }}"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a class="current-page" href="{{ route('anime.index') }}"><span>АНИМЕ</span></a>
    <p>/</p>
    <a class="current-page" href="{{ route('anime.season', ['season' => $season]) }}"><span>{{ $season }} СЕЗОН</span></a>
</div>
    <div class="cont scale-in" data-cover-description-card>
        <div class="image-wrapper">
            <picture>
                <source media="(max-width: 768px)" srcset="/images/anime/anime-banner-season-{{ $season }}-mobile.webp" type="image/webp">
                <img src="/images/anime/anime-banner-season-{{ $season }}.webp" decoding="async" alt="Обложка сезона">
            </picture>
        </div>
        <div class="desc slide-in-left">
            <h1>{{ $season }} СЕЗОН АНИМЕ-АДАПТАЦИИ</h1>
            <p>{!! $about_season->season_description ?? 'Описание сезона' !!}</p>
            <div class="low-buttons ">
                <button class="dropdown-btn no-glow" disabled aria-expanded="false">ДОБАВИТЬ В
                    <svg class="dropdown-icon">
                    <use href="#dropdown"></use>
                    </svg>
                </button>
                <a href="{{ route('anime.episode',['season'=>$season,'episode'=>1]) }}" class="link-like-button">НАЧАТЬ СМОТРЕТЬ</a>
                <a href="{{ $about_season->trailer_link }}" class="link-like-button no-glow">ТРЕЙЛЕР СЕЗОНА</a>
            </div>
            <div class="low-buttons mobile">
                <a href="{{ route('anime.episode',['season'=>$season,'episode'=>1]) }}" class="link-like-button">НАЧАТЬ СМОТРЕТЬ</a>
                <div class="double">
                    <button class="dropdown-btn no-glow" disabled aria-expanded="false">ДОБАВИТЬ В
                        <svg class="dropdown-icon">
                        <use href="#dropdown"></use>
                        </svg>
                    </button>
                    <a href="{{ $about_season->trailer_link }}" class="link-like-button no-glow">ТРЕЙЛЕР</a>
                </div>

            </div>
        </div>
    </div>
    <div class="cont2 scale-in">
        <div class="episodes-head">
            <button type="button" class="sort-toggle episode-control no-glow" aria-label="Сортировать по возрастанию/убыванию">
                <svg class="sort-descending sort-icon" aria-hidden="true" style="display: none;">
                    <use href="#sort-descending-filled-compact"></use>
                </svg>
                <svg class="sort-ascending sort-icon" aria-hidden="true">
                    <use href="#sort-ascending-filled-compact"></use>
                </svg>
                <span>СОРТИРОВКА</span>
            </button>
            <h1>СПИСОК СЕРИЙ</h1>
            <button type="button" class="filter-toggle episode-control dropdown-btn no-glow" disabled>
                <svg class="filter-icon" aria-hidden="true">
                    <use href="#filter-filled"></use>
                </svg>
                <span>ФИЛЬТР</span>
                <svg class="dropdown-icon" aria-hidden="true">
                    <use href="#dropdown"></use>
                </svg>
            </button>
        </div>
        <p class="info-schedule">Каждая новая серия выходит в <b>среду</b> в <b>15:30 по МСК</b>! Русские субтитры появляются на сайте спустя <b>полчаса-час</b>.</p>
        <div class="grid-area">
            @if (!empty($episodes))
                @foreach ($episodes as $index => $episode)
                    <div class="episode-cont">
                        <svg class="eye-filled" width="30" height="30">
                            <use href="#eye-filled"></use>
                        </svg>
                        <a class="card-link" href="{{ route('anime.episode',['season'=>$season,'episode'=>$episode->episode_number]) }}">
                            <div class="card-title mobile">
                                <h3>{{ $episode->episode_number }} серия</h3>
                                <p>{{ $episode->episode_name }}</p>
                            </div>
                            <div class="image-wrapper">
                                <img src="/images/anime/episodes-banner-season{{ $season }}.webp"></div>
                            <div class="card-title">
                                <h3>{{ $episode->episode_number }} серия</h3>
                                <p>{{ $episode->episode_name }}</p>
                            </div>
                            @if (!empty($episode->appear_in))
                                <div class="appear-in" style="display:flex;width:100%;flex-direction:column;margin-left:auto;margin-right:auto;text-align:center;">
                                    <p>До выхода серии:</p>
                                    <h3>6 дней 22 часа 11 минут</h3>
                                </div>
                            @endif
                        </a>
                        <div class="stars-and-comms">
                            <svg class="eye-filled mobile" width="30" height="30">
                                <use href="#eye-filled"></use>
                            </svg>
                            <a href="{{ $episode->trailer_link ?? '' }}"class="link-like-button no-glow trailer-link-mobile">ТРЕЙЛЕР</a>
                            <div class="first-btn">
                                @include('partials.rating', [
                                    'rateableType' => 'anime_episode',
                                    'rateableId' => $episode->id,
                                    'userRating' => optional($userRatings->get($episode->id))->rating ?? 0,
                                    'avgRating' => round((float) ($episode->avg_rating ?? 0), 1),
                                    'ratingsCount' => $episode->ratings_count ?? 0,
                                    'noExtra' => true,
                                ])
                            </div>
                            <a href="{{ $episode->trailer_link ?? '' }}"class="link-like-button no-glow trailer-link-desktop">ТРЕЙЛЕР СЕРИИ</a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    @endsection
