<script setup>
import { defineOptions, ref } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';
import '../../css/pages/about-project.css';
import Breadcrumbs from '../Components/Breadcrumbs.vue';
import Collapse from '../Components/Collapse.vue';
import { useScrollable } from '../Composables/useScrollable';

const props = defineProps({
    editors: {
        type: Array,
        default: () => [],
    },
    moderators: {
        type: Array,
        default: () => [],
    },
});

defineOptions({
    layout: AppLayout,
});

const editorMembersRef = ref(null);
const moderatorMembersRef = ref(null);

const { isScrollable: editorsScrollable } = useScrollable(editorMembersRef);
const { isScrollable: moderatorsScrollable } = useScrollable(moderatorMembersRef);
</script>

<template>

    <Breadcrumbs :items="[
        { text: 'ГЛАВНАЯ', href: route('home') },
        { text: 'О ПРОЕКТЕ' },
    ]" />

    <section class="team" aria-label="Основная команда проекта">
        <h1 class="main-title">КОМАНДА ПРОЕКТА</h1>
        <article class="team__member team__member--mregor4ik">
            <div class="team__portrait">
                <img fetchpriority="high" decoding="async" :src="'/images/about-project/team-mregor4ik.webp'" alt="Хорикита Сузуне">
            </div>
            <div class="team__card">
                <header class="team__heading">
                    <h1 class="team__name">MrEgor4ik</h1>
                    <div class="team__roles" aria-label="Роли">
                        <p class="team__role team__role--developer">веб-разработчик</p>
                        <p class="team__role team__role--devops">DevOps-инженер</p>
                    </div>
                </header>
                <div class="team__activity">
                    <h3 class="team__activity-title">Деятельность:</h3>
                    <div class="team__activity-list">
                        <p class="team__activity-item">Адаптивная и кроссбраузерная вёрстка макетов;</p>
                        <p class="team__activity-item">Администрирование доменных имён, файловой системы и баз данных;</p>
                        <p class="team__activity-item">Настройка серверного окружения, веб-сервера и инфраструктуры в целом;</p>
                        <p class="team__activity-item">Разработка архитектурных решений и внедрение нового функционала.</p>
                    </div>
                </div>
                <div class="team__quote">
                    <h3 class="team__quote-title">Личная цитата о проекте: </h3>
                    <p class="team__quote-text">«Есть лишь две вещи, на которые мне не п*хуй: первое - это деньги, второе - то, что я делаю».</p>
                </div>
            </div>
        </article>

        <article class="team__member team__member--andrey team__member--reverse">
            <div class="team__portrait">
                <img fetchpriority="high" decoding="async" :src="'/images/about-project/team-andrey.webp'" alt="Хиёри Шиина">
            </div>
            <div class="team__card">
                <header class="team__heading">
                    <h1 class="team__name">Andrey Andreev</h1>
                    <div class="team__roles" aria-label="Роли">
                        <p class="team__role team__role--designer">веб-дизайнер</p>
                        <p class="team__role team__role--manager">менеджер проекта</p>
                        <p class="team__role team__role--vibecoder">вайбкодер</p>
                        <p class="team__role team__role--sponsor">спонсор проекта</p>
                    </div>
                </header>
                <div class="team__activity">
                    <h3 class="team__activity-title">Деятельность:</h3>
                    <div class="team__activity-list">
                        <p class="team__activity-item">Создание макета, стилистики и логики дизайна, проработка UX/UI-части;</p>
                        <p class="team__activity-item">Техническая визуальная полировка и помощь разработчику с мелкими правками;</p>
                        <p class="team__activity-item">Поиск контента и заполнение информации для размещения на сайте;</p>
                        <p class="team__activity-item">Принятие решений по реализации тех или иных идей на сайте;</p>
                        <p class="team__activity-item">Финансовое спонсирование жизнедеятельности инфраструктуры сайта.</p>
                    </div>
                </div>
                <div class="team__quote">
                    <h3 class="team__quote-title">Личная цитата о проекте: </h3>
                    <p class="team__quote-text">«Это прекрасно, когда у масштабного произведения есть своя веб-обитель со своеобразной экосистемой, где можно найти абсолютно всё».</p>
                </div>
            </div>
        </article>

        <article class="team__member team__member--temzz">
            <div class="team__portrait">
                <img loading="lazy" decoding="async" :src="'/images/about-project/team-temzz.webp'" alt="Ичика Амасава">
            </div>
            <div class="team__card">
                <header class="team__heading">
                    <h1 class="team__name">TemZz</h1>
                    <div class="team__roles" aria-label="Роли">
                        <p class="team__role team__role--smm">SMM-менеджер</p>
                        <p class="team__role team__role--manager">основатель проекта</p>
                        <p class="team__role team__role--content">контент-куратор</p>
                    </div>
                </header>
                <div class="team__activity">
                    <h3 class="team__activity-title">Деятельность:</h3>
                    <div class="team__activity-list">
                        <p class="team__activity-item">Организация команды для реализации идеи сайта;</p>
                        <p class="team__activity-item">Продвижение проекта в медиа с помощью соцсетей;</p>
                        <p class="team__activity-item">Принятие решений по реализации тех или иных идей на сайте;</p>
                        <p class="team__activity-item">Условное владение брендом сайта, являясь его публичным лицом.</p>
                    </div>
                </div>
                <div class="team__quote">
                    <h3 class="team__quote-title">Личная цитата о проекте: </h3>
                    <p class="team__quote-text">«Я бы взял эту огромную з**ни*у Ичики Амасавы и ****** бы бл*** су*а».</p>
                </div>
            </div>
        </article>

        <article class="team__member team__member--einstein team__member--reverse">
            <div class="team__portrait">
                <img loading="lazy" decoding="async" :src="'/images/about-project/team-einstein.webp'" alt="Кацураги Кохэй">
            </div>
            <div class="team__card">
                <header class="team__heading">
                    <h1 class="team__name">Эйнштейн</h1>
                    <div class="team__roles" aria-label="Роли">
                        <p class="team__role team__role--content">главный контент-редактор</p>
                    </div>
                </header>
                <div class="team__activity">
                    <h3 class="team__activity-title">Деятельность:</h3>
                    <div class="team__activity-list">
                        <p class="team__activity-item">Поиск контента и ручное написание информации для размещения на сайте;</p>
                        <p class="team__activity-item">Генерация идей, систем и их сути для обсуждения внедрения на сайт;</p>
                        <p class="team__activity-item">Участие в принятии решений по реализации тех или иных идей на сайте.</p>
                    </div>
                </div>
                <div class="team__quote">
                    <h3 class="team__quote-title">Личная цитата о проекте: </h3>
                    <p class="team__quote-text">«Этот контент был создан специально для вас».</p>
                </div>
            </div>
        </article>
    </section>

    <section class="departments" aria-label="Отделы проекта">
        <Collapse :initially-open="true" class="department-card">
            <template #trigger>
                <h1 class="department-card__title">РЕДАКЦИЯ САЙТА</h1>
                <span class="card-arrow" aria-hidden="true">
                    <img :src="'/svgs/up.svg'" alt="">
                </span>
            </template>
            <p class="department-card__desc">Отдел контроля качества, укомплектованности и достоверности всего опубликованного на сайте контента. Редакция занимается заполнением информации, её корректировкой и актуализацией. Ответственным за работу коллектива является <strong>главный контент-редактор</strong>.</p>
            <div
                v-if="editors.length"
                ref="editorMembersRef"
                class="department-card__members"
                :class="{ 'is-scrollable': editorsScrollable }"
                aria-label="Редакторы сайта">
                <article v-for="editor in editors" :key="editor.nickname" class="department-card__member">
                    <img loading="lazy" decoding="async" :src="editor.avatar_url" :alt="`Аватар пользователя ${editor.nickname}`">
                    <p>{{ editor.nickname }}</p>
                </article>
            </div>
            <p v-else class="department-card__empty">В редакции пока нет пользователей.</p>
        </Collapse>

        <Collapse :initially-open="true" class="department-card">
            <template #trigger>
                <h1 class="department-card__title">МОДЕРАЦИЯ САЙТА</h1>
                <span class="card-arrow" aria-hidden="true">
                    <img :src="'/svgs/up.svg'" alt="">
                </span>
            </template>
            <p class="department-card__desc">Отдел контроля порядка и соблюдения правил сайта со стороны пользователей. Модерация следит за разделами комментариев, анализирует профили пользователей и обрабатывает жалобы и предложения. Ответственность за её деятельность несёт руководитель проекта.</p>
            <div
                v-if="moderators.length"
                ref="moderatorMembersRef"
                class="department-card__members"
                :class="{ 'is-scrollable': moderatorsScrollable }"
                aria-label="Модераторы сайта">
                <article v-for="moderator in moderators" :key="moderator.nickname" class="department-card__member">
                    <img loading="lazy" decoding="async" :src="moderator.avatar_url" :alt="`Аватар пользователя ${moderator.nickname}`">
                    <p>{{ moderator.nickname }}</p>
                </article>
            </div>
            <p v-else class="department-card__empty">В модерации пока нет пользователей.</p>
        </Collapse>
    </section>

    <section class="gratitude" aria-label="Благодарности">
        <Collapse :initially-open="true" class="gratitude-card">
            <template #trigger>
                <h1 class="gratitude-card__title">БЛАГОДАРНОСТИ</h1>
                <span class="card-arrow" aria-hidden="true">
                    <img :src="'/svgs/up.svg'" alt="">
                </span>
            </template>
            <div class="gratitude-card__content">
                <div class="gratitude-card__copy">
                    <p>Этот проект не появился бы без людей, которые вкладывали в него время, силы и ресурсы на разных этапах работы.</p>
                    <p>Мы благодарим <strong>Егора</strong> за огромный вклад в разработку: за техническую основу проекта, решение сложных задач и постоянную работу над тем, чтобы сайт развивался и становился удобнее.</p>
                    <p>Отдельная благодарность <strong>Темзу</strong> за организацию процессов, продвижение проекта и помощь в росте его популярности. Благодаря этой работе вокруг проекта постепенно сформировалось активное сообщество.</p>
                    <p>Мы благодарим <strong>Андрея</strong> за формирование визуального направления, работу с дизайном, заливку контента и финансовую поддержку проекта. Эти вещи часто остаются за кадром, но именно они помогают проекту выглядеть цельно и стабильно двигаться дальше.</p>
                    <p>Также выражаем благодарность <strong>Габи</strong> за помощь в подготовке подробных и полезных статей, работу с материалами и наполнение проекта контентом.</p>
                    <p>Спасибо всем, кто поддерживал нас со стороны: советом, обратной связью, распространением информации, участием в обсуждениях и простой заинтересованностью. Любая такая помощь влияет на развитие проекта и показывает, что работа делается не впустую.</p>
                </div>

                <div class="gratitude-card__feature">
                    <h2 class="gratitude-card__feature-title">Отдельного упоминания заслуживает испаноязычный энтузиаст, который занимается сканом иллюстраций в превосходском качестве.</h2>

                    <div class="gratitude-card__profiles" aria-label="Профили благодарности">
                        <a class="gratitude-card__profile" href="https://x.com/pix_targo" target="_blank" rel="noopener noreferrer">
                            <img loading="lazy" decoding="async" :src="'/images/about-project/gratitude-pixtargo.jpg'" alt="Аватар PixTargo">
                            <span class="gratitude-card__profile-name">PixTargo</span>
                            <span class="gratitude-card__profile-tag">@pix_targo</span>
                        </a>

                        <a class="gratitude-card__profile" href="https://x.com/love_art_2D" target="_blank" rel="noopener noreferrer">
                            <img loading="lazy" decoding="async" :src="'/images/about-project/gratitude-art-appreciator.jpg'" alt="Аватар Art appreciator">
                            <span class="gratitude-card__profile-name">Art appreciator</span>
                            <span class="gratitude-card__profile-tag">@love_art_2D</span>
                        </a>
                    </div>

                    <p class="gratitude-card__footer">Именно благодаря этому человеку вы можете лицезреть иллюстрации в прекрасном качестве на сайте и мы будем очень благодарны, если вы подпишитесь на него в Twitter (X.com) за его старания. Он будет безмерно рад приросту аудитории и пониманию, что его деятельность имеет отклик аудитории.</p>
                </div>
            </div>
        </Collapse>
    </section>
</template>