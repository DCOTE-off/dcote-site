@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/anime/season.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/rating.css') }}">

@endpush
@push('scripts')
    <script src="{{ asset('js/components/rating.js') }}"></script>
    <script src="{{ asset('js/pages/anime/season.js') }}"></script>
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
    <div class="cont scale-in">
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
        <div class="grid-area"
             id="episodes-list"
             data-collapsible-episodes
             data-season="{{ $season }}"
             data-server-now="{{ now()->toIso8601String() }}">
            @if (!empty($episodes))
                @foreach ($episodes as $index => $episode)
                    @php
                        // completed управляет доступностью; appear_in нужен только для автоматического выпуска и таймера.
                        $isUpcoming = !$episode->completed;
                        $hasReleaseDate = $isUpcoming && $episode->appear_in?->isFuture();
                    @endphp
                    <div class="episode-cont{{ $isUpcoming ? ' has-appear-in' : '' }}"
                         data-episode-number="{{ $episode->episode_number }}"
                         data-is-upcoming="{{ $isUpcoming ? 'true' : 'false' }}"
                         data-bookmark-state="unbookmarked"
                         @if ($hasReleaseDate)
                             data-appear-at="{{ $episode->appear_in->toIso8601String() }}"
                         @endif
                         @if (!$isUpcoming)
                             data-watch-state="unwatched"
                         @endif>
                        <a class="card-link" href="{{ route('anime.episode',['season'=>$season,'episode'=>$episode->episode_number]) }}">
                            <div class="card-title mobile">
                                <h3 class="episode-number">
                                    <span>{{ $episode->episode_number }} серия</span>
                                    <img class="open-in-new-tab-icon"
                                         src="{{ asset('svgs/open-in-new-tab.svg') }}"
                                         alt="Открыть серию в новой вкладке"
                                         role="link"
                                         tabindex="0"
                                         data-open-in-new-tab>
                                </h3>
                                <p>{{ $episode->episode_name }}</p>
                            </div>
                            <div class="image-wrapper{{ $isUpcoming ? ' has-appear-in' : '' }}">
                                <img class="episode-cover-image" src="/images/anime/episodes-banner-season{{ $season }}.webp">
                                {{-- Client-side bookmark placeholder.
                                     Current persistence: localStorage.
                                     Future backend integration point: hydrate data-bookmark-state and
                                     replace localStorage persistence in season.js. --}}
                                <span class="episode-bookmark-toggle episode-bookmark-toggle--mobile{{ $isUpcoming ? ' is-disabled' : '' }}"
                                      @if (!$isUpcoming)
                                          role="button"
                                          tabindex="0"
                                          aria-label="Добавить серию в закладки"
                                          aria-pressed="false"
                                          title="Добавить в закладки"
                                          data-bookmark-toggle
                                      @else
                                          aria-disabled="true"
                                          title="Закладка недоступна до выхода серии"
                                      @endif>
                                    <span class="episode-bookmark-icon" aria-hidden="true"></span>
                                </span>
                                {{-- Client-side watch state.
                                     Current persistence for released episodes: localStorage.
                                     Upcoming episodes render a disabled unwatched indicator.
                                     Future backend integration point: hydrate data-watch-state from
                                     per-user viewing progress and replace localStorage in season.js. --}}
                                <span class="episode-watch-state is-unwatched{{ $isUpcoming ? ' is-disabled' : '' }}"
                                      @if (!$isUpcoming)
                                          role="button"
                                          tabindex="0"
                                          aria-label="Отметить серию просмотренной"
                                          aria-pressed="false"
                                          title="Не просмотрено"
                                          data-watch-toggle
                                      @else
                                          aria-disabled="true"
                                          title="Просмотр недоступен до выхода серии"
                                      @endif>
                                    <img src="{{ asset('svgs/disabled-eye.svg') }}"
                                         @if (!$isUpcoming)
                                             data-watch-icon
                                             data-unwatched-src="{{ asset('svgs/disabled-eye.svg') }}"
                                             data-watched-src="{{ asset('svgs/eye.svg') }}"
                                         @endif
                                         alt=""
                                         aria-hidden="true">
                                </span>
                                @if ($isUpcoming)
                                    <div class="appear-in appear-in-mobile">
                                        <p>До выхода серии:</p>
                                        <h3 @if ($hasReleaseDate) data-episode-countdown @endif>
                                            {{ $hasReleaseDate ? '—' : 'Дата уточняется' }}
                                        </h3>
                                    </div>
                                @endif
                            </div>
                            <div class="card-title">
                                <h3 class="episode-number">
                                    <span>{{ $episode->episode_number }} серия</span>
                                    <img class="open-in-new-tab-icon"
                                         src="{{ asset('svgs/open-in-new-tab.svg') }}"
                                         alt="Открыть серию в новой вкладке"
                                         role="link"
                                         tabindex="0"
                                         data-open-in-new-tab>
                                </h3>
                                <p>{{ $episode->episode_name }}</p>
                                <div class="episode-meta-actions">
                                    <span class="episode-bookmark-toggle episode-bookmark-toggle--desktop{{ $isUpcoming ? ' is-disabled' : '' }}"
                                          @if (!$isUpcoming)
                                              role="button"
                                              tabindex="0"
                                              aria-label="Добавить серию в закладки"
                                              aria-pressed="false"
                                              title="Добавить в закладки"
                                              data-bookmark-toggle
                                          @else
                                              aria-disabled="true"
                                              title="Закладка недоступна до выхода серии"
                                          @endif>
                                        <span class="episode-bookmark-icon" aria-hidden="true"></span>
                                    </span>
                                    {{-- Temporary comments count placeholder. Replace 0 with the episode comment count. --}}
                                    <span class="episode-comments episode-comments--desktop{{ $isUpcoming ? ' is-disabled' : '' }}"
                                          aria-label="Комментариев: 0"
                                          title="Комментариев: 0">
                                        <img src="{{ asset('svgs/message1.svg') }}" alt="" aria-hidden="true">
                                        <span class="episode-comments-count">0</span>
                                    </span>
                                </div>
                            </div>
                        </a>
                        @if ($isUpcoming)
                            <a class="appear-in appear-in-desktop"
                               href="{{ route('anime.episode',['season'=>$season,'episode'=>$episode->episode_number]) }}">
                                <p>До выхода серии:</p>
                                <h3 @if ($hasReleaseDate) data-episode-countdown @endif>
                                    {{ $hasReleaseDate ? '—' : 'Дата уточняется' }}
                                </h3>
                            </a>
                        @endif
                        <div class="stars-and-comms">
                            <span class="episode-comments episode-comments--mobile{{ $isUpcoming ? ' is-disabled' : '' }}"
                                  aria-label="Комментариев: 0"
                                  title="Комментариев: 0">
                                <img src="{{ asset('svgs/message1.svg') }}" alt="" aria-hidden="true">
                                <span class="episode-comments-count">0</span>
                            </span>
                            <a href="{{ $episode->trailer_link ?? '' }}"class="link-like-button no-glow trailer-link-mobile">ТРЕЙЛЕР</a>
                            <div class="first-btn">
                                @include('partials.rating', [
                                    'rateableType' => 'anime_episode',
                                    'rateableId' => $episode->id,
                                    'userRating' => $isUpcoming ? 0 : (optional($userRatings->get($episode->id))->rating ?? 0),
                                    'avgRating' => $isUpcoming ? '0.0' : round((float) ($episode->avg_rating ?? 0), 1),
                                    'ratingsCount' => $isUpcoming ? 0 : ($episode->ratings_count ?? 0),
                                    'noExtra' => true,
                                    'disabled' => $isUpcoming,
                                ])
                            </div>
                            <a href="{{ $episode->trailer_link ?? '' }}"class="link-like-button no-glow trailer-link-desktop">ТРЕЙЛЕР СЕРИИ</a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button"
                class="episode-list-toggle dropdown-btn button-without-styles-all"
                aria-controls="episodes-list"
                aria-expanded="false"
                hidden>
            <span>Развернуть</span>
            <svg class="dropdown-icon" aria-hidden="true">
                <use href="#dropdown"></use>
            </svg>
        </button>
    </div>
    @endsection
