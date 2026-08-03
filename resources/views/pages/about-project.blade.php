@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/about-project.css')
@endpush

@push('scripts')
    @vite('resources/js/pages/about-project.js')
@endpush

@section('title', 'О проекте DCOTE | Наша команда')
@section('description', 'Познакомьтесь с командой DCOTE: разработчиками, дизайнерами и редакторами, которые создают проект о «Добро пожаловать в класс превосходства».')

@section('content')
    <div class="navigation-links">
        <a href="{{ route('home') }}"><span>ГЛАВНАЯ</span></a>
        <p>/</p>
        <a class="current-page" href="{{ route('about-project') }}"><span>О ПРОЕКТЕ</span></a>
    </div>

    <h1 class="main-title scale-in">КОМАНДА ПРОЕКТА</h1>

    <section class="project-team" aria-label="Основная команда проекта">
        <article class="team-member team-member--mregor4ik scale-in">
            <div class="member-portrait">
                <img fetchpriority="high" decoding="async" src="/images/about-project/team-mregor4ik.webp" alt="Хорикита Сузуне">
            </div>
            <div class="member-card slide-in-right">
                <header class="member-heading">
                    <h1>MrEgor4ik</h1>
                    <div class="role-list" aria-label="Роли">
                        <p class="role-tag role-tag--developer">веб-разработчик</p>
                        <p class="role-tag role-tag--devops">DevOps-инженер</p>
                    </div>
                </header>
                <div class="member-activity">
                    <h3>Деятельность:</h3>
                    <div class="activity-list">
                        <p>Адаптивная и кроссбраузерная вёрстка макетов;</p>
                        <p>Администрирование доменных имён, файловой системы и баз данных;</p>
                        <p>Настройка серверного окружения, веб-сервера и инфраструктуры в целом;</p>
                        <p>Разработка архитектурных решений и внедрение нового функционала.</p>
                    </div>
                </div>
                <div class="member-quote">
                    <h3>Личная цитата о проекте:</h3>
                    <p>«Есть лишь две вещи, на которые мне не п*хуй: первое - это деньги, второе - то, что я делаю».</p>
                </div>
            </div>
        </article>

        <article class="team-member team-member--andrey team-member--reverse scale-in">
            <div class="member-portrait">
                <img fetchpriority="high" decoding="async" src="/images/about-project/team-andrey.webp" alt="Хиёри Шиина">
            </div>
            <div class="member-card slide-in-left">
                <header class="member-heading">
                    <h1>Andrey Andreev</h1>
                    <div class="role-list" aria-label="Роли">
                        <p class="role-tag role-tag--designer">веб-дизайнер</p>
                        <p class="role-tag role-tag--manager">менеджер проекта</p>
                        <p class="role-tag role-tag--vibecoder">вайбкодер</p>
                        <p class="role-tag role-tag--sponsor">спонсор проекта</p>
                    </div>
                </header>
                <div class="member-activity">
                    <h3>Деятельность:</h3>
                    <div class="activity-list">
                        <p>Создание макета, стилистики и логики дизайна, проработка UX/UI-части;</p>
                        <p>Техническая визуальная полировка и помощь разработчику с мелкими правками;</p>
                        <p>Поиск контента и заполнение информации для размещения на сайте;</p>
                        <p>Принятие решений по реализации тех или иных идей на сайте;</p>
                        <p>Финансовое спонсирование жизнедеятельности инфраструктуры сайта.</p>
                    </div>
                </div>
                <div class="member-quote">
                    <h3>Личная цитата о проекте:</h3>
                    <p>«Это прекрасно, когда у масштабного произведения есть своя веб-обитель со своеобразной экосистемой, где можно найти абсолютно всё».</p>
                </div>
            </div>
        </article>

        <article class="team-member team-member--temzz scale-in">
            <div class="member-portrait">
                <img loading="lazy" decoding="async" src="/images/about-project/team-temzz.webp" alt="Ичика Амасава">
            </div>
            <div class="member-card slide-in-right">
                <header class="member-heading">
                    <h1>TemZz</h1>
                    <div class="role-list" aria-label="Роли">
                        <p class="role-tag role-tag--smm">SMM-менеджер</p>
                        <p class="role-tag role-tag--manager">основатель проекта</p>
                        <p class="role-tag role-tag--content">контент-куратор</p>
                    </div>
                </header>
                <div class="member-activity">
                    <h3>Деятельность:</h3>
                    <div class="activity-list">
                        <p>Организация команды для реализации идеи сайта;</p>
                        <p>Продвижение проекта в медиа с помощью соцсетей;</p>
                        <p>Принятие решений по реализации тех или иных идей на сайте;</p>
                        <p>Условное владение брендом сайта, являясь его публичным лицом.</p>
                    </div>
                </div>
                <div class="member-quote">
                    <h3>Личная цитата о проекте:</h3>
                    <p>«Я бы взял эту огромную з**ни*у Ичики Амасавы и ****** бы бл*** су*а».</p>
                </div>
            </div>
        </article>

        <article class="team-member team-member--einstein team-member--reverse scale-in">
            <div class="member-portrait">
                <img loading="lazy" decoding="async" src="/images/about-project/team-einstein.webp" alt="Кацураги Кохэй">
            </div>
            <div class="member-card slide-in-left">
                <header class="member-heading">
                    <h1>Эйнштейн</h1>
                    <div class="role-list" aria-label="Роли">
                        <p class="role-tag role-tag--content">главный контент-редактор</p>
                    </div>
                </header>
                <div class="member-activity">
                    <h3>Деятельность:</h3>
                    <div class="activity-list">
                        <p>Поиск контента и ручное написание информации для размещения на сайте;</p>
                        <p>Генерация идей, систем и их сути для обсуждения внедрения на сайт;</p>
                        <p>Участие в принятии решений по реализации тех или иных идей на сайте.</p>
                    </div>
                </div>
                <div class="member-quote">
                    <h3>Личная цитата о проекте:</h3>
                    <p>«Этот контент был создан специально для вас».</p>
                </div>
            </div>
        </article>
    </section>

    <section class="project-departments scale-in" aria-label="Отделы проекта">
        <details class="department-card expandable-card" open>
            <summary>
                <h1>РЕДАКЦИЯ САЙТА</h1>
                <span class="department-arrow" aria-hidden="true">
                    <img src="/svgs/up.svg" alt="">
                </span>
            </summary>
            <p>Отдел контроля качества, укомплектованности и достоверности всего опубликованного на сайте контента. Редакция занимается заполнением информации, её корректировкой и актуализацией. Ответственным за работу коллектива является <strong>главный контент-редактор</strong>.</p>
            <div class="department-members" aria-label="Редакторы сайта">
                @forelse($editors as $editor)
                    <article class="department-member">
                        <img loading="lazy" decoding="async" src="{{ $editor->avatar_url }}" alt="Аватар пользователя {{ $editor->nickname }}">
                        <p>{{ $editor->nickname }}</p>
                    </article>
                @empty
                    <p class="department-empty">В редакции пока нет пользователей.</p>
                @endforelse
            </div>
            <div class="department-scrollbar" aria-hidden="true">
                <span class="department-scrollbar-thumb"></span>
            </div>
        </details>

        <details class="department-card expandable-card" open>
            <summary>
                <h1>МОДЕРАЦИЯ САЙТА</h1>
                <span class="department-arrow" aria-hidden="true">
                    <img src="/svgs/up.svg" alt="">
                </span>
            </summary>
            <p>Отдел контроля порядка и соблюдения правил сайта со стороны пользователей. Модерация следит за разделами комментариев, анализирует профили пользователей и обрабатывает жалобы и предложения. Ответственность за её деятельность несёт руководитель проекта.</p>
            <div class="department-members" aria-label="Модераторы сайта">
                @forelse($moderators as $moderator)
                    <article class="department-member">
                        <img loading="lazy" decoding="async" src="{{ $moderator->avatar_url }}" alt="Аватар пользователя {{ $moderator->nickname }}">
                        <p>{{ $moderator->nickname }}</p>
                    </article>
                @empty
                    <p class="department-empty">В модерации пока нет пользователей.</p>
                @endforelse
            </div>
            <div class="department-scrollbar" aria-hidden="true">
                <span class="department-scrollbar-thumb"></span>
            </div>
        </details>
    </section>

    <section class="project-gratitude scale-in" aria-label="Благодарности">
        <details class="gratitude-card expandable-card" open>
            <summary>
                <h1>БЛАГОДАРНОСТИ</h1>
                <span class="department-arrow" aria-hidden="true">
                    <img src="/svgs/up.svg" alt="">
                </span>
            </summary>

            <div class="gratitude-content">
                <div class="gratitude-copy">
                    <p>Этот проект не появился бы без людей, которые вкладывали в него время, силы и ресурсы на разных этапах работы.</p>
                    <p>Мы благодарим <strong>Егора</strong> за огромный вклад в разработку: за техническую основу проекта, решение сложных задач и постоянную работу над тем, чтобы сайт развивался и становился удобнее.</p>
                    <p>Отдельная благодарность <strong>Темзу</strong> за организацию процессов, продвижение проекта и помощь в росте его популярности. Благодаря этой работе вокруг проекта постепенно сформировалось активное сообщество.</p>
                    <p>Мы благодарим <strong>Андрея</strong> за формирование визуального направления, работу с дизайном, заливку контента и финансовую поддержку проекта. Эти вещи часто остаются за кадром, но именно они помогают проекту выглядеть цельно и стабильно двигаться дальше.</p>
                    <p>Также выражаем благодарность <strong>Габи</strong> за помощь в подготовке подробных и полезных статей, работу с материалами и наполнение проекта контентом.</p>
                    <p>Спасибо всем, кто поддерживал нас со стороны: советом, обратной связью, распространением информации, участием в обсуждениях и простой заинтересованностью. Любая такая помощь влияет на развитие проекта и показывает, что работа делается не впустую.</p>
                </div>

                <h2 class="gratitude-feature-title">Отдельного упоминания заслуживает испаноязычный энтузиаст, который занимается сканом иллюстраций в превосходном качестве.</h2>

                <div class="gratitude-profiles" aria-label="Профили благодарности">
                    <a class="gratitude-profile no-glow" href="https://x.com/pix_targo" target="_blank" rel="noopener noreferrer">
                        <img loading="lazy" decoding="async" src="/images/about-project/gratitude-pixtargo.jpg" alt="Аватар PixTargo">
                        <span class="gratitude-profile-name">PixTargo</span>
                        <span class="gratitude-profile-tag">@pix_targo</span>
                    </a>

                    <a class="gratitude-profile no-glow" href="https://x.com/love_art_2D" target="_blank" rel="noopener noreferrer">
                        <img loading="lazy" decoding="async" src="/images/about-project/gratitude-art-appreciator.jpg" alt="Аватар Art appreciator">
                        <span class="gratitude-profile-name">Art appreciator</span>
                        <span class="gratitude-profile-tag">@love_art_2D</span>
                    </a>
                </div>

                <p class="gratitude-footer">Именно благодаря этому человеку вы можете лицезреть иллюстрации в прекрасном качестве на сайте и мы будем очень благодарны, если вы подпишитесь на него в Twitter (X.com) за его старания. Он будет безмерно рад приросту аудитории и пониманию, что его деятельность имеет отклик аудитории.</p>
            </div>
        </details>
    </section>
@endsection
