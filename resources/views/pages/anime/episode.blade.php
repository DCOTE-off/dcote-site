@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/anime/episode.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/rating.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/pages/anime/episode.js') }}"></script>
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
    @if (!$completed)
        <h1 style="text-align: center;">СЕРИИ ПОКА НЕТ</h1>
    @else
            <iframe
                src="{{ $episodeUrl }}"
                allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"
                style="border-radius: var(--fs-border-radius);width:min-content;aspect-ratio:16/9;max-height:95vh;border:0px;align-self:center"
                loading="lazy"
                id="episode-iframe-player">
            </iframe>
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
