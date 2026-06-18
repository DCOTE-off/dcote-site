@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/rules.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/components/rating.js') }}"></script>
@endpush
@section('title', 'DCOTE | Политика конфиденциальности')
@section('description', 'Политика конфиденциальности сайта DCOTE')
@section('content')
    @include('partials.rating', [
        'rateableType' => 'anime_episode',
        'rateableId' => 2,
        'userRating' => 7,
        'avgRating' => 8.5,
        'ratingsCount' => 142,
    ])
@endsection