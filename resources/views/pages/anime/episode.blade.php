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
    <div class="subs-and-dubs">
        <button data-type="dub" data-src="{{ $dubUrl }}" class="dubs {{ $initial_type === 'dub' ? 'active' : '' }}" {{ !$has_dub ? 'disabled' : '' }}>ОЗВУЧКА</button>
        <button data-type="sub" data-src="{{ $subUrl }}" class="subs {{ $initial_type === 'sub' ? 'active' : '' }}" {{ !$has_sub ? 'disabled' : '' }}>СУБТИТРЫ</button>
    </div>
    @if (!$has_dub && !$has_sub)
        <h1 style="text-align: center;">СЕРИИ ПОКА НЕТ</h1>
    @else
        <iframe
            src="{{ $initial_type === 'dub' ? $dubUrl : $subUrl }}"
            allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            style="border-radius: var(--fs-border-radius); width: 100%; aspect-ratio: 16/9;border:0px"
            loading="lazy"
            id="episode-iframe-player">
        </iframe>
    @endif
    <div class="episode-controls">
        <a class="link-like-button {{ $episode <= 1 ? 'disabled_a' : '' }}"  href="{{ route('anime.episode', ['season' => $season,'episode'=>$episode-1]) }}" class="prev-episode-btn">ПРЕДЫДУЩАЯ СЕРИЯ</a>
        <a class="link-like-button" href="{{ route('anime.season',['season'=>$season]) }}">ВСЕ СЕРИИ</a>
        <a class="link-like-button {{ $episode >= $total_episodes ? 'disabled_a' : '' }}" href="{{ route('anime.episode', ['season' => $season,'episode'=>$episode+1]) }}" class="next-episode-btn">СЛЕДУЮЩАЯ СЕРИЯ</a>
    </div>
    <div class="episode-controls mobile">
        <a class="link-like-button {{ $episode <= 1 ? 'disabled_a' : '' }}"  href="{{ route('anime.episode', ['season' => $season,'episode'=>$episode-1]) }}" class="prev-episode-btn">
            <svg class="slider-icon" width="30" height="30">
                <use href="#arrow-left"></use>
            </svg>
        </a>
        <a class="link-like-button" href="{{ route('anime.season',['season'=>$season]) }}">
            <svg class="slider-icon" width="30" height="30">
                <use href="#list-details"></use>
            </svg>
        </a>
        <a class="link-like-button {{ $episode >= $total_episodes ? 'disabled_a' : '' }}" href="{{ route('anime.episode', ['season' => $season,'episode'=>$episode+1]) }}" class="next-episode-btn">
            <svg class="slider-icon" width="30" height="30">
                <use href="#arrow-right"></use>
            </svg>
        </a>
    </div>
    @endsection