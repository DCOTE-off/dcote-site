@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dcote-main.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/pages/dcote_main.js') }}"></script>
@endpush
@section('title', 'DCOTE | Вики, новости и контент по «Классу Превосходства»')
@section('content')
    <div class="hero">
        <img class="hero-image" src="/images/index/hero-блок.webp" fetchpriority="high" decoding="async" alt="Арису лежит в кроватке" />
        <div class="hero-content">
            <div class="title">
                <h1>Фан-сообщество <span style="color: rgb(224, 11, 82);">D</span>COTE</h1>
                <h3>Обитель фанатского комьюнити произведения «Добро пожаловать в класс превосходства».</h3>
            </div>
            <p>Горячие новости из медиа-пространства произведения, чтение оригинальной новеллы,
                бесплатный просмотр аниме-адаптации и чтение глав манги. Полноценный сборник
                иллюстраций от художника Томосе Сюнсаку, арты от художников-фанатов
                и превосходные арт-генерации от ИИ. Подробные досье и описания персонажей.
                Всё это и не только вы найдёте на страницах данного веб-сообщества!</p>
            <div class="hero-buttons">
                <a class="link-like-button">ЧИТАТЬ НОВОСТИ</a>
                <a href="{{ route('about-project') }}" class="link-like-button project-btn">О ПРОЕКТЕ</a>
                <a href="https://t.me/DCOTEFILES" target="_blank" class="link-like-button tg-btn" rel="noopener noreferrer">ТЕЛЕГРАМ-КАНАЛ</a>
            </div>
        </div>
    </div>
    <div class="hero mobile">
        <img class="hero-image" src="/images/index/hero-mobile.webp" fetchpriority="high" decoding="async" alt="Арису лежит в кроватке" />
        <div class="hero-content">
            <div class="title-and-smth">
                <div class="title">
                    <h1>Фан-сообщество <span style="color: rgb(224, 11, 82);">D</span>COTE</h1>
                </div>
                <p>Горячие новости, чтение ранобэ и/или манги и просмотр аниме. Сборник иллюстраций
                    и подробные досье всех персонажей.
                    Всё это и не только, вы найдёте на данном веб-сайте!</p>
            </div>
            <div class="hero-buttons">
                <a href="{{ route('about-project') }}" class="link-like-button project-btn" rel="noopener noreferrer">О ПРОЕКТЕ</a>
                <a href="https://t.me/DCOTEFILES" target="_blank" class="link-like-button tg-btn" rel="noopener noreferrer">ТЕЛЕГРАМ-КАНАЛ</a>
            </div>
        </div>
    </div>
    <div class="grid-images">
        <a href="{{ route('anime.index') }}">
            <div class="wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/index/category-anime-mobile.webp" type="image/webp">
                    <img src="/images/index/category-anime.webp" alt="Категория Аниме">
                </picture>
                <div class="content">
                    <h3><b>АНИМЕ</b></h3>
                    <p>Бесплатный просмотр аниме-адаптации всех сезонов в хорошем качестве</p>
                </div>
            </div>
        </a>
        <a href="{{ route('ranobe.index') }}">
            <div class="wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/index/category-ranobe-mobile.webp" type="image/webp">
                    <img src="/images/index/category-ranobe.webp" alt="Категория Ранобэ">
                </picture>
                <div class="content">
                    <h3><b>РАНОБЭ</b></h3>
                    <p>Чтение оригинальной новеллы в полном формате и хорошем качестве перевода</p>
                </div>
            </div>
        </a>
        <a href="{{ route('manga.index') }}">
            <div class="wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/index/category-manga-bw-mobile.webp" type="image/webp">
                    <img src="/images/index/category-manga-bw.webp" alt="Категория Манга">
                </picture>
                <div class="content">
                    <h3><b>МАНГА</b></h3>
                    <p>Чтение глав манга-адаптации в хорошем качестве изображений и перевода</p>
                </div>
            </div>
        </a>
        <a href="{{ route('illustrations.index') }}">
            <div class="wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/index/category-illustrations-bw-mobile.webp" type="image/webp">
                    <img src="/images/index/category-illustrations-bw.webp" alt="Категория Иллюстрации">
                </picture>
                <div class="content">
                    <h3><b>ИЛЛЮСТРАЦИИ</b></h3>
                    <p>Сборник иллюстраций от художника Томосе Сюнсаку, арты от художников-фанатов и превосходные арт-генерации от ИИ</p>
                </div>
            </div>
        </a>
        <a href="{{ route('characters.index') }}">
            <div class="wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/index/category-characters-bw-mobile.webp" type="image/webp">
                    <img src="/images/index/category-characters-bw.webp" alt="Категория Персонажи">
                </picture>
                <div class="content">
                    <h3><b>ПЕРСОНАЖИ</b></h3>
                    <p>Подробные досье и описания всех персонажей произведения</p>
                </div>
            </div>
        </a>
    </div>
    <div class="content-line1">
        <div class="popular">
            <h1>ПОПУЛЯРНОЕ</h1>
            <div class="pager">
                <button class="pager-btn inactive" disabled aria-label="Назад">
                    <svg class="slider-icon" width="30" height="30">
                        <use href="#arrow-left"></use>
                    </svg>
                </button>
                <div class="content">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/index/аниме-4-сезон.webp" type="image/webp">
                    <img src="/images/index/аниме-4-сезон.webp" loading="lazy" decoding="async">
                </picture>
                    <div class="text">
                        <h3><b>АНИМЕ</b> 4 СЕЗОН</h3>
                        <p>Токийская столичная старшая школа продвинутого воспитания —
                            словно школа мечты: практически 100% дальнейшее поступление
                            в вузы и трудоустройство после выпуска, а каждый месяц
                            выплачиваются баллы, эквивалентные 100.000 йен наличными.
                            Однако на деле это место, где привилегии получают лишь немногие
                            ученики с выдающимися результатами...</p>
                        <a href="{{ route('anime.season', ['season' => 4]) }}" class="link-like-button" rel="noopener noreferrer">ПОДРОБНЕЕ О СЕЗОНЕ</a>
                        <a href="{{ route('anime.season', ['season' => 4]) }}" class="link-like-button mobile" rel="noopener noreferrer">ПОДРОБНЕЕ</a>
                    </div>
                </div>
                <button class="pager-btn inactive" disabled aria-label="Назад">
                    <svg class="slider-icon" width="30" height="30">
                        <use href="#arrow-right"></use>
                    </svg>
                </button>
            </div>
        </div>
        <div class="updates scale-in">
            <div class="updates-title">
                <h1>ЛЕНТА ОБНОВЛЕНИЙ</h1>
            </div>
            <div class="updates-news">
                @foreach ($feed as $index => $update)
                    <div>
                        @php
                            $isFirst = $index == 0;
                            $title = $isFirst ? 'НОВОЕ' : format_date($update->created_at);
                            $class = $isFirst ? 'hot' : '';
                        @endphp
                        <h3 class="{{ $class }}">{{ $title }}</h3>
                        <div class="one-news-wrapper">
                            <a href="/{{ $update->link }}" style="line-height: 1.4em">
                                <p>{{ $update->description }}</p>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="updates-link-wrapper">
                <a class="link-like-button disabled_a" rel="noopener noreferrer">ВСЕ ОБНОВЛЕНИЯ</a>
            </div>
        </div>
    </div>
    <div class="content-line4">
        <div class="top-classes scale-in">
            <div class="title">
                <h1>РЕЙТИНГ КЛАССОВ</h1>
            </div>
            <div class="spoilers-btn-cont">
                <button class="spoilers-btn no-glow button-without-styles">
                    <span>БЕЗ СПОЙЛЕРОВ</span>
                    <span>СО СПОЙЛЕРАМИ</span>
                </button>
            </div>
            <div class="rating">
                @foreach ($classes_list_default as $class)
                        <div class="school-class">
                            <img src="{{ $class->leader_img }}" loading="lazy" decoding="async">
                            <div class="info">
                                <div class="text">
                                    <h3>Класс {{ $class->letter }}</h3>
                                    <p>{{ $class->leader }}</p>
                                </div>
                                <div class="points-bg" style="--points-width: {{ $class->percent }}%; background: {{ $class->color }};">
                                    <p><b>{{ $class->class_points }}</b> классных очков</p>
                                </div>
                            </div>
                        </div>
                @endforeach
            </div>
            <div class="rating spoilers hidden">
                @foreach ($classes_list_spoilers as $class)
                    <div class="school-class">
                        <img src="{{ $class->leader_img }}" loading="lazy" decoding="async">
                        <div class="info">
                            <div class="text">
                                <h3>Класс {{ $class->letter }}</h3>
                                <p>{{ $class->leader }}</p>
                            </div>
                            <div class="points-bg" style="--points-width: {{ $class->percent }}%;background: {{ $class->color }};">
                                <p><b>{{ $class->class_points }}</b> классных очков</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="full-stat"><button disabled>ПОЛНАЯ СТАТИСТИКА</button></div>
        </div>
        <div class="top-users scale-in">
            <div class="title">
                <h1>РЕЙТИНГ ПОЛЬЗОВАТЕЛЕЙ</h1>
            </div>
            <div class="users-table">
                <table style="height: 100%;">
                    <thead>
                        <tr>
                            <th class="rank-head" style="width:10%;">
                                <p>Ранг</p>
                            </th>
                            <th class="" style="width:8%;"></th>
                            <th class="nickname-head" style="width:32%;">
                                <p>Пользователь</p>
                            </th>
                            <th class="status-head" style="width:35%;">
                                <p>Статус</p>
                            </th>
                            <th class="rating-on-main-head" style="width:14%;">
                                <p>Рейтинг</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="height: 100%;">
                        <tr>
                            <td class="rank"><svg class="chess-icon" style="color:#ffb147">
                                    <use href="#chess-king"></use>
                                </svg>
                                <p><b>1</b></p>
                            </td>
                            <td><img src="/images/user-avatar.webp" alt=""></td>
                            <td class="nickname" data-status="-">
                                <p>-</p>
                            </td>
                            <td class="status">
                                <p style="color: white;">-</p>
                            </td>
                            <td class="rating-on-main">
                                <p>700</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="rank"><svg class="chess-icon" style="color:#b2beca">
                                    <use href="#chess-queen"></use>
                                </svg>
                                <p><b>2</b></p>
                            </td>
                            <td><img src="/images/user-avatar.webp" alt=""></td>
                            <td class="nickname" data-status="-">
                                <p>-</p>
                            </td>
                            <td class="status">
                                <p style="color: white;">-</p>
                            </td>
                            <td class="rating-on-main">
                                <p>600</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="rank"><svg class="chess-icon" style="color:#ce8947">
                                    <use href="#chess-queen"></use>
                                </svg>
                                <p><b>3</b></p>
                            </td>
                            <td><img src="/images/user-avatar.webp" alt=""></td>
                            <td class="nickname" data-status="-">
                                <p>-</p>
                            </td>
                            <td class="status">
                                <p style="color: white;">-</p>
                            </td>
                            <td class="rating-on-main">
                                <p>500</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="rank"><svg class="chess-icon" style="color:white">
                                    <use href="#chess-rook"></use>
                                </svg>
                                <p><b>4</b></p>
                            </td>
                            <td><img src="/images/user-avatar.webp" alt=""></td>
                            <td class="nickname" data-status="-">
                                <p>-</p>
                            </td>
                            <td class="status">
                                <p style="color: white;">-</p>
                            </td>
                            <td class="rating-on-main">
                                <p>400</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="rank"><svg class="chess-icon" style="color:white">
                                    <use href="#chess-rook"></use>
                                </svg>
                                <p><b>5</b></p>
                            </td>
                            <td><img src="/images/user-avatar.webp" alt=""></td>
                            <td class="nickname" data-status="-">
                                <p>-</p>
                            </td>
                            <td class="status">
                                <p style="color: white;">-</p>
                            </td>
                            <td class="rating-on-main">
                                <p>300</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="rank"><svg class="chess-icon" style="color:white">
                                    <use href="#chess-rook"></use>
                                </svg>
                                <p><b>6</b></p>
                            </td>
                            <td><img src="/images/user-avatar.webp" alt=""></td>
                            <td class="nickname" data-status="-">
                                <p>-</p>
                            </td>
                            <td class="status">
                                <p style="color: white;">-</p>
                            </td>
                            <td class="rating-on-main">
                                <p>200</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="rank"><svg class="chess-icon" style="color:white">
                                    <use href="#chess-rook"></use>
                                </svg>
                                <p><b>7</b></p>
                            </td>
                            <td><img src="/images/user-avatar.webp" alt=""></td>
                            <td class="nickname" data-status="-">
                                <p>-</p>
                            </td>
                            <td class="status">
                                <p style="color: white;">-</p>
                            </td>
                            <td class="rating-on-main">
                                <p>100</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="full-stat"><button disabled>ВСЕ ПОЛЬЗОВАТЕЛИ</button></div>
        </div>
    </div>
    <div class="some-info">
        <div class="description">
            <div class="image-wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/index/na-divane-mobile.webp" type="image/webp">
                    <img src="/images/index/na-divane.webp" alt="На диване">
                </picture>
            </div>
            <div class="text">
                <div class="title-and-smth">
                    <h1>ОПИСАНИЕ ПРОИЗВЕДЕНИЯ</h1>
                    <p>
                        Добро пожаловать в класс превосходства — это напряжённая школьная драма
                        с элементами психологического триллера, действие которой разворачивается
                        в престижной государственной школе Кодо Икусэй, известной идеальными условиями
                        и почти гарантированным будущим успехом для выпускников. Однако за внешним
                        совершенством скрывается жестокая система ранжирования, где учащиеся получают
                        всё — от привилегий до денежных баллов — строго по заслугам и результатам конкуренции.
                        <br><br>
                        Главный герой, таинственный и замкнутый Аянокоджи Киётака, по воле обстоятельств
                        оказывается в худшем классе D, куда отправляют «дефективных» учеников школы.
                        Несмотря на намерение оставаться в тени, он постепенно оказывается втянут в
                        сложную игру интриг, стратегий и скрытых конфликтов между учениками школы.
                    </p>
                </div>
                <div style="display: flex;width: 100%;justify-content: center;"><a class="link-like-button disabled_a" rel="noopener noreferrer">БОЛЬШЕ ИНФОРМАЦИИ</a></div>
            </div>
        </div>
        <div class="description">
            <div class="image-wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/index/shkola-mobile.webp" type="image/webp">
                    <img src="/images/index/shkola.webp" alt="Школа">
                </picture>
            </div>
            <div class="text">
                <div class="title-and-smth">
                    <h1>СТОЛИЧНАЯ СТАРШАЯ ШКОЛА ПРОДВИНУТОГО ВОСПИТАНИЯ</h1>
                    <p>
                        Токийское государственное учебное учреждение, созданное японским правительством
                        для воспитания молодых выпускников, которые в будущем будут поддерживать различные
                        профессиональные отрасли страны, что подкрепляется особыми методами обучения.
                        За счёт своей репутации, школа может похвастаться своим практически сто процентным уровнем
                        занятости и возможным поступлением в престижный колледж или университет. Сам кампус располагается на отдельном,
                        искусственно сконструированном острове, площадь которого составляет около шестиста тысяч квадратных метров.
                        <br><br>
                        Примечательно, что председателем совета директоров данного учебного заведения является Сакаянаги Наримори — отец Сакаянаги Арису.
                    </p>
                </div>
                <div style="display: flex;width: 100%;justify-content: center;"><a class="link-like-button" href="{{ route('about-school') }}" rel="noopener noreferrer">ПОДРОБНАЯ ИНФОРМАЦИЯ</a></div>
            </div>
        </div>
    </div>
    <div class="content-line3">
        <div class="about-island slide-in-right">
            <div class="title" style="text-align: center;">
                <h1>ЭКЗАМЕНАЦИОННЫЙ ШКОЛЬНЫЙ НЕОБИТАЕМЫЙ ОСТРОВ</h1>
            </div>
            <div class="text">
                <p>Необитаемый остров, что юридически принадлежит школе и периодически используется руководством для проведения
                    специальных экзаменов.<br><br>
                    Проведение таких экзаменов по традиции выпадает на начало нового года и знаменует собой всю серьёзность
                    выстроенной правительством школьной системой - выживание в диких условиях, работа в команде, противостояние группам оппонентов.
                    <br><br>Сам остров делится на специальные сектора, что предназначены для реального использования на специфичных по
                    правилам экзаменах.<br><br>Путешествия на остров происходят каждый год, что позволяет
                    выработать у учеников некую адаптацию к подобным условиям.
                </p>
            </div>
        </div>
        <div class="island-image-base scale-in">
            <picture>
                <source media="(max-width: 768px)" srcset="/images/index/остров-mobile.webp" type="image/webp">
                <img src="/images/index/остров.webp" loading="lazy" decoding="async" alt="Крутой остров фото скачать" />
            </picture>
        </div>
    </div>
    <div class="tg-links">
        <div class="banner">
            <div class="image-wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/index/обсуждение-новостей-mobile.webp" type="image/webp">
                    <img src="/images/index/обсуждение-новостей.webp" loading="lazy" decoding="async" alt="Обсуждение новостей">
                </picture>
            </div>
            <div class="text">
                <div class="title-and-smth">
                    <h1>ОБСУЖДЕНИЕ НОВОСТЕЙ</h1>
                    <p class="tg-banner-description"><span>Беседа в Telegram, где люди обсуждают</span><span>актуальные новости и беседуют</span><span>друг с другом</span></p>
                </div>
                <div class="tg-links-buttons">
                    <a href="https://t.me/DCOTEFILES" class="link-like-button tg-channel-btn" target="_blank" rel="noopener noreferrer">ТЕЛЕГРАМ-КАНАЛ</a>
                    <a href="https://t.me/DCOTE2" class="link-like-button tg-chat-btn" target="_blank" rel="noopener noreferrer">ТЕЛЕГРАМ-ЧАТ</a>
                </div>
            </div>
        </div>
        <div class="banner">
            <div class="image-wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/index/обсуждение-спойлеров-mobile.webp" type="image/webp">
                    <img src="/images/index/обсуждение-спойлеров.webp" loading="lazy" decoding="async" alt="Обсуждение спойлеров">
                </picture>
            </div>
            <div class="text">
                <div class="title-and-smth">
                    <h1>ОБСУЖДЕНИЕ СПОЙЛЕРОВ</h1>
                    <p class="tg-banner-description"><span>Беседа в Telegram, где люди обсуждают</span><span>спойлеры, беседуют друг с другом</span><span>и комфортно проводят время</span></p>
                </div>
                <div class="tg-links-buttons">
                    <a href="https://t.me/DCOTESPOILERS2" class="link-like-button tg-channel-btn" target="_blank" rel="noopener noreferrer">ТЕЛЕГРАМ-КАНАЛ</a>
                    <a href="https://t.me/DCOTESPOILERSCHAT" class="link-like-button tg-chat-btn" target="_blank" rel="noopener noreferrer">ТЕЛЕГРАМ-ЧАТ</a>
                </div>
            </div>
        </div>
        <div class="banner">
            <div class="image-wrapper">
                <picture>
                    <source media="(max-width: 768px)" srcset="/images/index/теории-и-разборы-mobile.webp" type="image/webp">
                    <img src="/images/index/теории-и-разборы.webp" loading="lazy" decoding="async" alt="Теории и разборы">
                </picture>
            </div>
            <div class="text">
                <div class="title-and-smth">
                    <h1>ТЕОРИИ И ВАЖНЫЕ РАЗБОРЫ</h1>
                    <p class="tg-banner-description"><span>Обсуждение горячих тем и разборов,</span><span>как в ключевых, так и второстепенных</span><span>фрагментах произведения</span></p>
                </div>
                <div class="tg-links-buttons">
                    <a href="https://t.me/DCOTETHEORIES" class="link-like-button tg-channel-btn" target="_blank" rel="noopener noreferrer">ТЕЛЕГРАМ-КАНАЛ</a>
                    <a href="https://t.me/DCOTETHEORIES2" class="link-like-button tg-chat-btn" target="_blank" rel="noopener noreferrer">ТЕЛЕГРАМ-ЧАТ</a>
                </div>
            </div>
        </div>
    </div>
    @endsection
