<script setup>
import { defineOptions } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import '../../css/components/comments.css';

defineOptions({
    layout: AppLayout,
});

const comments = [
    {
        id: 1,
        username: 'Andrey Andreev',
        role: 'Команда проекта',
        isTeam: true,
        rating: 199,
        text: 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fuga numquam, optio id impedit amet eos? Facilis ipsa, temporibus dolore, fugit, sapiente tempore quos maiores explicabo vero natus similique! Minima, voluptatibus.',
        date: '13.04.2026; 17:24',
        withAnswers: false,
    },
    {
        id: 2,
        username: 'Andrey Andreev',
        role: 'Команда проекта',
        isTeam: true,
        rating: 199,
        text: 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fuga numquam, optio id impedit amet eos? Facilis ipsa, temporibus dolore, fugit, sapiente tempore quos maiores explicabo vero natus similique! Minima, voluptatibus.',
        date: '13.04.2026; 17:24',
        withAnswers: true,
    },
];
</script>

<template>
    <section class="comments">
        <div class="comments__header">
            <button type="button" class="comments__filter no-glow">ФИЛЬТР</button>
            <h1 class="comments__title">КОММЕНТАРИИ</h1>
            <Link class="comments__rules link-like-button no-glow" :href="route('rules')">ПРАВИЛА САЙТА</Link>
        </div>

        <div class="comments__input"></div>

        <div class="comments__list">
            <article
                v-for="comment in comments"
                :key="comment.id"
                class="comment"
                :class="{ 'comment--with-answers': comment.withAnswers }">
                <div class="comment__head">
                    <div class="comment__user">
                        <img class="comment__avatar" :src="'/images/user-avatar.webp'" :alt="comment.username">
                        <div class="comment__user-info">
                            <h3 class="comment__username">{{ comment.username }}</h3>
                            <p
                                class="comment__role"
                                :class="{ 'comment__role--team': comment.isTeam }">
                                {{ comment.role }}
                            </p>
                        </div>
                    </div>
                    <div class="comment__rating">
                        <button type="button" class="comment__rating-btn button-without-styles-all">
                            <img :src="'/svgs/up.svg'" alt="Повысить рейтинг">
                        </button>
                        <p class="comment__rating-value" :class="{ 'comment__rating-value--positive': comment.rating > 0 }">
                            {{ comment.rating > 0 ? '+' : '' }}{{ comment.rating }}
                        </p>
                        <button type="button" class="comment__rating-btn button-without-styles-all">
                            <img class="comment__rating-icon--down" :src="'/svgs/up.svg'" alt="Понизить рейтинг">
                        </button>
                    </div>
                    <button
                        v-if="comment.withAnswers"
                        type="button"
                        class="comment__answers-toggle button-without-styles-all">
                        <svg class="comment__answers-icon"><use href="#dropdown" /></svg>
                    </button>
                </div>
                <div class="comment__content">
                    <p>{{ comment.text }}</p>
                </div>
                <div class="comment__actions">
                    <div class="comment__actions-group">
                        <p>Ответить</p>
                        <p>Пожаловаться</p>
                    </div>
                    <p class="comment__date">{{ comment.date }}</p>
                </div>
            </article>
        </div>
    </section>
</template>
