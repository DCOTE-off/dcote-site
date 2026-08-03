@extends('layouts.app') 
@push('styles')
    @vite(['resources/css/pages/anime/episode.css', 'resources/css/components/dropdown.css', 'resources/css/components/rating.css', 'resources/css/components/player-status.css'])
@endpush
@push('scripts')
    @vite(['resources/js/components/rating.js', 'resources/js/pages/anime/episode.js'])
@endpush
@section('title', "«Класс превосходства» {$season} сезон {$episode} серия | Смотреть онлайн | DCOTE")
@section('description', "Смотреть онлайн {$episode} серию {$season} сезона аниме «Добро пожаловать в класс превосходства». Видео в хорошем качестве и обсуждение серии на DCOTE.")
@section('content')
<div class="navigation-links">
    <a href="{{ route('home') }}"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a class="current-page" href="{{ route('anime.index') }}"><span>АНИМЕ</span></a>
    <p>/</p>
    <a class="current-page" href="{{ route('anime.season', ['season' => $season]) }}"><span>{{ $season }} СЕЗОН</span></a>
    <p>/</p>
    <a class="current-page" href="{{ route('anime.episode', ['season' => $season,'episode'=>$episode]) }}"><span>{{ $episode }} СЕРИЯ</span></a>
</div>
    @if (isset($episodeModel) && $completed)
        <div style="width: max-content;align-self:center">
            @include('partials.rating', [
                'rateableType' => 'anime_episode',
                'rateableId' => $episodeModel->id,
                'userRating' => $userRating ?? 0,
                'avgRating' => round((float) ($episodeAvgRating ?? 0), 1),
                'ratingsCount' => $episodeRatingsCount ?? 0,
            ])
        </div>
    @endif
    @if (!$completed)
        <h1 style="text-align: center;">СЕРИИ ПОКА НЕТ</h1>
    @else
        <div class="episode-player-shell">
            <iframe
                src="{{ $episodeUrl }}"
                allow="autoplay; encrypted-media; picture-in-picture"
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"
                loading="lazy"
                id="episode-iframe-player">
            </iframe>
            <div class="episode-player-status" id="episode-player-status" role="status" hidden>
                <h2>ПЛЕЕР ВРЕМЕННО НЕДОСТУПЕН</h2>
                <p>Проверьте соединение и попробуйте загрузить его ещё раз.</p>
                <button class="link-like-button" type="button" id="episode-player-retry">
                    ПОВТОРИТЬ
                </button>
            </div>
        </div>
    @endif
    <div class="episode-controls">
        <a class="link-like-button episode-nav-button prev-episode-btn {{ !$prev_link ? 'disabled_a' : '' }}"  href="{{ $prev_link }}">
            <svg class="episode-arrow-icon" aria-hidden="true">
                <use href="#arrow-left"></use>
            </svg>
            <span>ПРЕДЫДУЩАЯ СЕРИЯ</span>
        </a>
        <a class="link-like-button no-glow all-episodes-btn" href="{{ route('anime.season',['season'=>$season]) }}">
            <span>ВСЕ СЕРИИ</span>
            <svg class="episode-arrow-icon episode-arrow-icon-down" aria-hidden="true">
                <use href="#arrow-down"></use>
            </svg>
        </a>
        <a class="link-like-button episode-nav-button next-episode-btn {{ !$next_link ? 'disabled_a' : '' }}" href="{{ $next_link }}">
            <span>СЛЕДУЮЩАЯ СЕРИЯ</span>
            <svg class="episode-arrow-icon" aria-hidden="true">
                <use href="#arrow-right"></use>
            </svg>
        </a>
    </div>
    <div class="episode-controls mobile">
        <a class="link-like-button episode-nav-button prev-episode-btn {{ !$prev_link ? 'disabled_a' : '' }}"  href="{{ $prev_link }}">
            <svg class="episode-arrow-icon" aria-hidden="true">
                <use href="#arrow-left"></use>
            </svg>
            <span>НАЗАД</span>
        </a>
        <a class="link-like-button no-glow all-episodes-btn" href="{{ route('anime.season',['season'=>$season]) }}">
            <span>СЕРИИ</span>
            <svg class="episode-arrow-icon episode-arrow-icon-down" aria-hidden="true">
                <use href="#arrow-down"></use>
            </svg>
        </a>
        <a class="link-like-button episode-nav-button next-episode-btn {{ !$next_link ? 'disabled_a' : '' }}" href="{{ $next_link }}">
            <span>ВПЕРЁД</span>
            <svg class="episode-arrow-icon" aria-hidden="true">
                <use href="#arrow-right"></use>
            </svg>
        </a>
    </div>
    @endsection
