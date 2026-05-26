@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/page-404.css') }}">
@endpush
@section('content')
    <div class="wrapper">
        <h1 class="text-404">503</h1>
        <img class="ptichka" src="/images/errors/ptichka-500.webp" alt="бро из мема">
        <div class="desc-404">
            <h2>ПРОБЛЕМАТИЧНО, САЙТ СЕЙЧАС ОБСЛУЖИВАЕТСЯ</h2>
            <div class="button"><a class="return link-like-button" href="{{ route('home') }}">
                    <h3>НА ГЛАВНУЮ</h3>
            </a></div>
        </div>
    </div>
    @endsection