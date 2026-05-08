@extends('layouts.app') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/about-project.css') }}">
@endpush
@section('title', 'О проекте DCOTE | Наша команда')
@section('description', 'Познакомьтесь с командой DCOTE. Участники проекта, переводчики, редакторы и разработчики, которые делают мир «Добро пожаловать в класс превосходства» доступнее.')
@section('content')
<div class="navigation-links">
    <a href="{{ route('home') }}"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a class="current-page" href="{{ route('about-project') }}"><span>О ПРОЕКТЕ</span></a>
</div>
    <h1 class="main-title scale-in">КОМАНДА ПРОЕКТА</h1>
    <div class="text-wrapper scale-in ">
        <div class="img-wrapper">
            <img fetchpriority="high" decoding="async" src="/images/about-project/game-banner horikita5.webp" alt="Хорикита Сузуне">
        </div>
        <div class="text slide-in-right">
            <div class="title">
                <h1>MrEgor4ik</h1>
                <p>full-stack разработчик</p>
            </div>
            <div class="desc">
                <h3>Деятельность:</h3>
                <p>
                    - Полная разработка и архитектура проекта (frontend и backend);<br><br>
                    - Реализация анимаций и интерактивных элементов;<br><br>
                    - Управление серверером, файловой системой, хостингом и базой данных;<br><br>
                    - Владение и администрирование домена проекта.
                </p>
            </div>
            <p class="quote">Цитата: «Вообще я хотел заниматься только бекендом, но судьба распорядилась иначе, теперь я мастер на все руки. Дизайнер кстати ахуел».</p>
        </div>
    </div>
    <div class="text-wrapper scale-in reverse">
        <div class="img-wrapper">
            <img fetchpriority="high" decoding="async" src="/images/about-project/game-banner hiyori2.webp" alt="Хиери Шиина">
        </div>
        <div class="text slide-in-left">
            <div class="title">
                <h1>Andrey Andreev</h1>
                <p>веб-дизайнер, контент-куратор</p>
            </div>
            <div class="desc">
                <h3>Деятельность:</h3>
                <p>- Ответственен за визуальную часть каждой страницы сайта;<br><br>
                    - Поиск материалов и источников для заполнения информации и контента;<br><br>
                    - Проработка дизайна интерфейса с динамическим отображением.</p>
            </div>
            <p class="quote">Цитата: «Я дизайнер и я хочу, чтобы всё было идеально симметрично и красиво».</p>
        </div>
    </div>

    <div class="text-wrapper scale-in">
        <div class="img-wrapper">
            <img loading="lazy" decoding="async" src="/images/about-project/game-banner katsuragi2.webp" alt="Кацураги Кохэй">
        </div>
        <div class="text slide-in-left">
            <div class="title">
                <h1>Эйнштейн</h1>
                <p>контент-менеджер</p>
            </div>
            <div class="desc">
                <h3>Деятельность:</h3>
                <p>- Поиск материалов и источников для заполнения информации и контента;<br><br>
                    - Ручное написание альфа-версий текстового контента;<br><br>
                    - Участие в проработке UI/UX аспектах.</p>
            </div>
            <p class="quote">Цитата: «Этот контент был создан специально для тебя».</p>
        </div>
    </div>
    <div class="text-wrapper scale-in reverse">
        <div class="img-wrapper">
            <img loading="lazy" decoding="async" src="/images/about-project/game-banner karuizawa2.webp" alt="Каруидзава Кей">
        </div>
        <div class="text slide-in-right">
            <div class="title">
                <h1>TemZz</h1>
                <p>маркетолог, SMM-специалист</p>
            </div>
            <div class="desc">
                <h3>Деятельность:</h3>
                <p>
                    - Формирование и организация команды для разработки проекта;<br><br>
                    - Работа с продвижением сайта через активные медиа-ресурсы;<br><br>
                    - Обсуждение и принятие ключевых аспектов тех или иных отраслей сайта.
                </p>
            </div>
            <p class="quote">Цитата: «Я бы взял эту огромную з**ни*у Ичики Амасавы и ****** бы бл*** су*а».</p>
        </div>
    </div>
    <div class="text-wrapper scale-in">
        <div class="img-wrapper">
            <img loading="lazy" decoding="async" src="/images/about-project/game-banner ryuen1.webp" alt="Рьюен Какеру">
        </div>
        <div class="text slide-in-left">
            <div class="title">
                <h1>Popco1n</h1>
                <p>концепт-автор</p>
            </div>
            <div class="desc">
                <h3>Деятельность:</h3>
                <p>- Создание альфа версии визуальной части сайта. Формирование концепта идеи и её вектора;<br><br>
                    - Поиск материалов и источников для заполнения
                    информации и контента.</p>
            </div>
            <p class="quote">Цитата: «Любите и др*чите на Рьюена».</p>
        </div>
    </div>
    <div class="text-wrapper scale-in reverse">
        <div class="img-wrapper">
            <img loading="lazy" decoding="async" src="/images/about-project/game-banner kanzaki3.webp" alt="Канзаки Рюджи">
        </div>
        <div class="text slide-in-right">
            <div class="title">
                <h1>Vinader</h1>
                <p>веб-разработчик</p>
            </div>
            <div class="desc">
                <h3>Деятельность:</h3>
                <p>
                    - Акцент на работе с фронтэнд частью сайта;<br><br>
                    - Обсуждение и принятие ключевых аспектов тех или иных отраслей сайта.
                </p>
            </div>
            <p class="quote">Цитата: «Кирьюин наступи на меня».</p>
        </div>
    </div>
    @endsection