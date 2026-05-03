@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/page-404.css') }}">
@endpush
@section('content')
    <div class="wrapper">
        <h1 class="text-404">404</h1>
        <img class="ptichka" src="/images/errors/ptichka-404.webp" alt="бро из мема">
        <div class="desc-404">
            <h2>СОМНЕВАЮСЬ, ЧТО ТАКАЯ СТРАНИЦА СУЩЕСТВУЕТ</h2>
            <div class="button"><a class="return link-like-button" href="{{ route('home') }}">
                    <h3>НА ГЛАВНУЮ</h3>
            </a></div>
        </div>
    </div>
    @endsection