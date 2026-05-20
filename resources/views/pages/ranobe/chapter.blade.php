@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/ranobe/chapter.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/rating.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown-menu.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/components/dropdown-menu.js') }}"></script>
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
