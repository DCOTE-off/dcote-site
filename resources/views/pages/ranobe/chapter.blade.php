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
</div>
    @endsection
