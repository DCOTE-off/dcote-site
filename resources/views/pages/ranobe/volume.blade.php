@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/components/rating.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdown.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('css/pages/ranobe/volume.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/pages/anime/season.js') }}"></script>
    <script src="https://unpkg.com/embla-carousel/embla-carousel.umd.js"></script>
    <script src="{{ asset('js/pages/ranobe/volume.js') }}"></script>
@endpush
@section('title', "Читать ранобэ «Класс превосходства» {$year} год {$volume_number_rounded} том | DCOTE")
@section('description', "Читать {$year} год {$volume_number_rounded} год ранобэ «Добро пожаловать в класс превосходства» онлайн. Описание тома, список глав и даты выхода на сайте DCOTE.")
@section('content')
<div class="navigation-links">
    <a href="{{ route('home') }}"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a href="{{ route('ranobe.index') }}"><span>РАНОБЭ</span></a>
    <p>/</p>
    <a href="{{ route('ranobe.year',['year'=>$year]) }}"><span>{{ $year }} ГОД</span></a>
    <p>/</p>
    <a href="{{ route('ranobe.volume',['year'=>$year,'volume'=>$volume_number_rounded]) }}"><span>{{ $volume_number_rounded }} ТОМ</span></a>
</div>
    <div class="volume-cont scale-in">
        <div class="image-wrapper">
            <picture>
                <source media="(max-width: 768px)" 
                        srcset="{{ Storage::url($volumeModel->cover_image_mobile) }}" 
                        type="image/webp">
                <img src="{{ Storage::url($volumeModel->cover_image) }}" 
                    fetchpriority="high"
                    decoding="async" 
                    alt="Обложка {{$volume_number_rounded}} тома {{ $year }} года">
            </picture>
        </div>
        <div class="desc slide-in-left">
            <h1>{{ $volume_number_rounded }} ТОМ {{ $year }} ГОДА ОБУЧЕНИЯ</h1>
            <p>{!! $volumeModel->volume_description ?? 'Описание {{$volume_number_rounded }} тома {{ $year }} года обучения'!!}</p>
            <div class="bottom-buttons">
                <button class="dropdown-btn no-glow" disabled aria-expanded="false">ДОБАВИТЬ В
                    <svg class="dropdown-icon">
                    <use href="#dropdown"></use>
                    </svg>
                </button>
                <a href="{{route('ranobe.chapter', ['year'=>$year,'volume'=>$volume_number_rounded,'chapter'=>1]) }}" class="link-like-button">НАЧАТЬ ЧИТАТЬ</a>
                <a href="{{ $volumeModel->promo_link ?? ''}}" class="link-like-button no-glow">ПРОМО ТОМА</a>
            </div>
            <div class="bottom-buttons mobile">
                <a href="{{route('ranobe.chapter', ['year'=>$year,'volume'=>$volume_number_rounded,'chapter'=>1]) }}" class="link-like-button">НАЧАТЬ ЧИТАТЬ</a>
                <div class="double">
                    <button class="dropdown-btn no-glow" disabled aria-expanded="false">ДОБАВИТЬ В
                        <svg class="dropdown-icon">
                        <use href="#dropdown"></use>
                        </svg>
                    </button>
                    <a href="{{ $volumeModel->promo_link ?? '' }}" class="link-like-button no-glow">ПРОМО ТОМА</a>
                </div>
            </div>
        </div>
    </div>
    <div class="volume-info">
        <div class="chapters-cont scale-in">
            <div class="chapters-head">
                <h1>ОГЛАВЛЕНИЕ</h1>
                <button type="button" class="sort-toggle button-without-styles-all" aria-label="Сортировать по возрастанию/убыванию">
                    <svg class="sort-descending" width="20" height="20" style="display: none;">
                        <use href="#sort-descending-filled"></use>
                    </svg>
                    <svg class="sort-ascending" width="20" height="20">
                        <use href="#sort-ascending-filled"></use>
                    </svg>
                </button>
            </div>
            <div class="grid-area">
                @if (!empty($chapters))
                    @foreach ($chapters as $index => $chapter)
                        <a class="link-like-button no-glow chapter-button"
                            href="{{route('ranobe.chapter', ['year'=>$year,'volume'=>$volume_number_rounded,'chapter'=>floatval($chapter->chapter_number)]) }}">
                            {!! Illuminate\Support\Str::markdown($chapter->title) !!}
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="volume-images-cont">
            <h1>ИЛЛЮСТРАЦИИ К ТОМУ</h1>
            <div class="group-carousels-cont">
                <div class="title-carousel-cont">
                    <h3>ЦВЕТНЫЕ ВЕРСИИ</h3>
                    <div class="carousel-container">
                        <div class="embla">
                            <div class="embla__container">
                                @foreach ($color_images as $image )
                                    <div class="embla__slide">
                                        <img src="{{ $image['url'] }}"class="carousel-img"
                                        data-download="{{ $image['download_url'] ?? $image['url'] }}" >
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="title-carousel-cont">
                    <h3>ЧЁРНО-БЕЛЫЕ ВЕРСИИ</h3>
                    <div class="carousel-container">
                        <div class="embla">
                            <div class="embla__container">
                                @foreach ($bw_images as $image )
                                    <div class="embla__slide">
                                        <img src="{{ $image['url'] }}" class="carousel-img"
                                        data-download="{{ $image['download_url'] ?? $image['url'] }}" >
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div id="imageModal" class="modal">
        <span class="modal-close" aria-label="Закрыть окно">
            <img src="{{ asset('svgs/close.svg') }}" alt="Закрыть">
        </span>
        <a class="modal-download" href="#" download aria-label="Скачать изображение">
            <img src="{{ asset('svgs/download.svg') }}" alt="Скачать">
        </a>
        <span class="modal-next" aria-label="Следующее изображение">
            <img src="{{ asset('svgs/caret-right.svg') }}" alt="Следующий">
        </span>
        <span class="modal-prev" aria-label="Предыдущее изображение">
            <img src="{{ asset('svgs/caret-left.svg') }}" alt="Предыдущий">
        </span>
        <img class="modal-content" id="modalTargetImg">
        <div id="modalCaption"></div>
        <div class="modal-overlay"></div>
    </div>
    @endsection