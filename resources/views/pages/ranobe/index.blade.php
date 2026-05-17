@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/ranobe/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/components/dropdown-menu.js') }}"></script>
@endpush
@section('title', 'Читать ранобэ «Класс превосходства» | Все года | DCOTE')
@section('description', 'Список всех годов ранобэ «Добро пожаловать в класс превосходства». Выбирайте год и приступайте к чтению с хорошим переводом на DCOTE.')
@section('content')
<svg style="display: none;">
    <symbol id="check-circle" viewBox="0 0 25 25">
        <path d="M18.7498 1.68062C24.7247 5.12975 26.7747 12.7778 23.3248 18.7513C19.8748 24.7248 12.2249 26.7743 6.24993 23.3252C2.51247 21.1632 0.149998 17.2267 0 12.9028V12.103C0.224998 5.20474 5.99994 -0.206399 12.8999 0.00604749C14.9498 0.0685317 16.9623 0.643387 18.7498 1.66813V1.68062ZM17.1373 9.11625C16.6873 8.66636 15.9873 8.62887 15.4873 9.01627L15.3748 9.11625L11.2624 13.2277L9.6499 11.6156L9.5374 11.5156C8.98741 11.0907 8.21241 11.1907 7.78742 11.7406C7.43742 12.1905 7.43742 12.8153 7.78742 13.2777L7.88742 13.3902L10.3874 15.8895L10.4999 15.9895C10.9499 16.3394 11.5874 16.3394 12.0374 15.9895L12.1499 15.8895L17.1498 10.8908L17.2498 10.7783C17.6373 10.2785 17.5873 9.57863 17.1498 9.12875L17.1373 9.11625Z" fill="#56CC64"/>
    </symbol>
    <symbol id="clock-logo" viewBox="0 0 25 25">
        <path d="M18.7498 1.68062C24.7247 5.12975 26.7747 12.7778 23.3248 18.7513C19.8748 24.7248 12.2249 26.7743 6.24993 23.3252C2.38748 21.0882 0 16.9643 0 12.5029V12.103C0.224998 5.20474 5.99994 -0.206399 12.8999 0.00604749C14.9498 0.0685317 16.9623 0.643387 18.7498 1.66813M12.4999 5.00479C11.8124 5.00479 11.2499 5.56714 11.2499 6.25447V12.6654L11.2874 12.8028L11.3374 12.9653L11.3999 13.0902L11.4624 13.1902L11.5124 13.2652L11.5999 13.3652L11.7124 13.4652L11.8124 13.5401L15.5623 16.0395C16.1373 16.4269 16.9123 16.2644 17.2998 15.6896C17.6873 15.1147 17.5248 14.3399 16.9498 13.9525L13.7499 11.8281V6.25447C13.7499 5.61713 13.2749 5.09226 12.6499 5.01728H12.4999V5.00479Z" fill="#FFB147"/>
    </symbol>
</svg>
<div class="navigation-links">
    <a href="{{ route('home') }}"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a class="current-page" href="{{ route('ranobe.index') }}"><span>РАНОБЭ</span></a>
</div>
<div class="years">
    @foreach ($years as $index => $year)
    <div class="year-cont year-{{ $year->year_number }}">
        <img src="/images/ranobe/years/y{{$year->year_number}}v1.webp" class="year-img">
        <div class="year-desc">
            <h1>{{$year->year_readable}}</h1>
            <div class="info">
                <button class="dropdown-menu-btn no-glow" aria-expanded="false" data-target="menu-{{ $index }}">ПОДРОБНАЯ ИНФОРМАЦИЯ
                    <svg class="dropdown-icon">
                    <use href="#dropdown"></use>
                    </svg>
                </button>
                <div class="dropdown-wrapper" id="menu-{{ $index }}">
                    <div class="dropdown-content">
                        <dl class="info-block">
                            <dt>Всего томов:</dt>
                            <dd>{{ $year->volumes_count }}</dd>
                            <dt>Всего глав:</dt>
                            <dd>{{ $year->chapters_count }}</dd>
                            <dt>Всего страниц:</dt>
                            <dd>{{ $year->total_pages ?? 0 }}</dd>
                            <dt>Всего слов:</dt>
                            <dd>{{ $year->words_quantity }}</dd>
                            <dt>Время чтения:</dt>
                            <dd>{{ $year->hours_of_reading }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <a class="link-like-button" style="align-self:center" href="{{ route('ranobe.year',['year'=>$year->year_number]) }}">ПЕРЕЙТИ</a>
            <div class="year-status">
                <svg class="status-icon">
                    @if($year->status == 'Завершён')
                        <use href="#check-circle"></use>
                    @else
                        <use href="#clock-logo"></use>
                    @endif
                </svg>
                <p @if($year->status == 'Завершён') class="green" @else class="yellow" @endif>
                    {{$year->status}}
                </p>
            </div>
        </div>
    </div>
    @endforeach
</div>
    @endsection