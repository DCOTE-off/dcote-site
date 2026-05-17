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
@section('title', "Читать «Класс превосходства» | {$year} год {$volume_number_rounded} том {$chapter} | DCOTE")
@section('description', "Читать {$chapterModel->title} {$volume_number_rounded} тома новеллы «Добро пожаловать в класс превосходства». Читайте с высоким качеством перевода на DCOTE.")
@section('content')
<div class="chapter-content">
    <h3 class="main-title">Том {{ $volume_number_rounded }} - {!! strip_tags(Illuminate\Support\Str::markdown($chapterModel->title)) !!}</h3>
    <article class="ranobe-content">
        {!! $htmlContent !!}
    </article>
</div>
    @endsection
