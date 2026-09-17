<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import CommentInput from './CommentInput.vue';
import { useIsMobile} from '../Composables/useMediaQuery';

const props = defineProps({
    comment: {
        type: Object,
        required: true,
    },
    depth: {
        type: Number,
        default: 0,
    },
    replyTo: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['reply', 'vote', 'save-edit', 'remove']);

const repliesOpen = ref(false);
const replies = computed(() => props.comment.replies ?? []);
const hasReplies = computed(() => replies.value.length > 0);
const isRoot = computed(() => props.depth === 0);
const showReplies = computed(() => hasReplies.value && (!isRoot.value || repliesOpen.value));
const moreActionsOpen = ref(false);
const moreActionsEl = ref(null);

const isMobile = useIsMobile();

function closeMoreActions() {
    moreActionsOpen.value = false;
}

function onMoreActionsPointerDown(event) {
    if (!moreActionsEl.value?.contains(event.target)) {
        closeMoreActions();
    }
}

function onMoreActionsKeydown(event) {
    if (event.key === 'Escape') {
        closeMoreActions();
    }
}

watch(moreActionsOpen, (open) => {
    if (open) {
        document.addEventListener('pointerdown', onMoreActionsPointerDown);
        document.addEventListener('keydown', onMoreActionsKeydown);
    } else {
        document.removeEventListener('pointerdown', onMoreActionsPointerDown);
        document.removeEventListener('keydown', onMoreActionsKeydown);
    }
});

onBeforeUnmount(() => {
    document.removeEventListener('pointerdown', onMoreActionsPointerDown);
    document.removeEventListener('keydown', onMoreActionsKeydown);
});

function countReplies(list) {
    return list.reduce((sum, reply) => sum + 1 + countReplies(reply.replies ?? []), 0);
}

const totalReplies = computed(() => countReplies(replies.value));

function pluralizeReplies(count) {
    const mod10 = count % 10;
    const mod100 = count % 100;

    if (mod10 === 1 && mod100 !== 11) {
        return `${count} ответ`;
    }

    if (mod10 >= 2 && mod10 <= 4 && (mod100 < 12 || mod100 > 14)) {
        return `${count} ответа`;
    }

    return `${count} ответов`;
}

const repliesLabel = computed(() => pluralizeReplies(totalReplies.value));

const editing = ref(false);
const editDraft = ref('');

function startEdit() {
    editDraft.value = props.comment.text ?? '';
    editing.value = true;
}

function cancelEdit() {
    editing.value = false;
    editDraft.value = '';
}

function submitEdit() {
    if (editDraft.value.trim() === '') return;

    emit('save-edit', { comment: props.comment, content: editDraft.value });
    cancelEdit();
}

function vote(value) {
    emit('vote', { comment: props.comment, vote: value });
}

function toggleSpoiler(target) {
    const spoiler = target?.closest?.('.comment__spoiler');
    if (spoiler) {
        spoiler.classList.toggle('is-open');
    }
}

function onContentClick(event) {
    if (event.target.closest('a')) return;
    toggleSpoiler(event.target);
}

function onContentKeydown(event) {
    if (event.key !== 'Enter' && event.key !== ' ') return;
    if (!event.target.closest('.comment__spoiler')) return;

    event.preventDefault();
    toggleSpoiler(event.target);
}
</script>

<template>
    <article
        class="comment"
        :class="{ 'comment--has-replies': hasReplies, 'comment--reply': depth > 0 }">
        <div class="comment__head">
            <div class="comment__user">
                <img class="comment__avatar" :src="comment.avatar || '/images/user-avatar.webp'" :alt="comment.username">
                <div class="comment__user-info">
                    <h3 class="comment__username">{{ comment.username }}</h3>
                    <span class="comment__role" :class="{ 'comment__role--team': comment.isTeam }">{{ comment.role }}</span>
                </div>
            </div>
            <div class="comment__rating">
                <button
                    type="button"
                    class="comment__rating-btn rating-up"
                    :class="{ 'comment__rating-btn--active': comment.userVote === 1 }"
                    aria-label="Повысить рейтинг"
                    @click="vote(1)">
                    <svg class="comment__rating-icon" aria-hidden="true"><use href="#chevron-up" /></svg>
                </button>
                <span class="comment__rating-value" :class="{ 'comment__rating-value--positive': comment.rating > 0,
                    'comment__rating-value--negative': comment.rating < 0
                  }">
                    {{ comment.rating > 0 ? '+' : '' }}{{ comment.rating }}
                </span>
                <button
                    type="button"
                    class="comment__rating-btn rating-down"
                    :class="{ 'comment__rating-btn--active': comment.userVote === -1 }"
                    aria-label="Понизить рейтинг"
                    @click="vote(-1)">
                    <svg class="comment__rating-icon comment__rating-icon--down" aria-hidden="true"><use href="#chevron-up" /></svg>
                </button>
            </div>
        </div>
        <div class="comment__content">
            <CommentInput
                v-if="editing"
                v-model="editDraft"
                autofocus
                @submit="submitEdit"
                @cancel="cancelEdit"
                class="editing-input" />
            <span v-if="replyTo" class="comment__reply-to">Ответ на «{{ replyTo }}»</span><p v-if="!editing" v-html="comment.html || comment.text" @click="onContentClick" @keydown="onContentKeydown"></p>
        </div>
        <div class="comment__actions">
            <div class="comment__actions-group">
                <button v-if="!editing && !comment.isDeleted" type="button" class="comment__action" @click="emit('reply', comment)">Ответить</button>
                <template v-if="!isMobile">
                    <button v-if="comment.can?.edit" type="button" class="comment__action" @click="startEdit">Редактировать</button>
                    <button v-if="comment.can?.delete" type="button" class="comment__action" @click="emit('remove', comment)">Удалить</button>
                    <!-- <button v-if="!editing && !comment.isDeleted" type="button" class="comment__action">Пожаловаться</button> -->
                </template>
                <div ref="moreActionsEl" class="comment__more-actions">
                    <button
                        v-if="isMobile && (comment.can?.edit || comment.can?.delete)"
                        type="button"
                        class="comment__more-actions-btn"
                        aria-label="Действия с комментарием"
                        aria-haspopup="menu"
                        :aria-expanded="moreActionsOpen"
                        @click="moreActionsOpen = !moreActionsOpen">
                        <svg class="comment__more-actions-icon" aria-hidden="true"><use href="#ellipsis" /></svg>
                    </button>
                    <div v-if="isMobile && moreActionsOpen" class="comment__more-actions-dd" role="menu" @click="closeMoreActions">
                        <button v-if="comment.can?.edit" type="button" class="comment__action" role="menuitem" @click="startEdit">Редактировать</button>
                        <button v-if="comment.can?.delete" type="button" class="comment__action" role="menuitem" @click="emit('remove', comment)">Удалить</button>
                    </div>
                </div>
            </div>
            <p class="comment__date">{{ comment.date }}<template v-if="comment.isEdited"> · ред.</template></p>
        </div>

        <slot name="reply" :comment="comment" />

        <div v-if="showReplies" class="comment__replies">
            <CommentItem
                v-for="reply in replies"
                :key="reply.id"
                :comment="reply"
                :depth="depth + 1"
                :reply-to="comment.username"
                @reply="emit('reply', $event)"
                @vote="emit('vote', $event)"
                @save-edit="emit('save-edit', $event)"
                @remove="emit('remove', $event)">
                <template #reply="slotProps">
                    <slot name="reply" v-bind="slotProps" />
                </template>
            </CommentItem>
        </div>
        <button
            v-if="isRoot && hasReplies"
            type="button"
            class="comment__answers-toggle"
            :aria-expanded="repliesOpen"
            aria-label="Показать/скрыть ответы"
            @click="repliesOpen = !repliesOpen">{{ repliesOpen ? 'Свернуть' : repliesLabel }}
            <svg class="comment__answers-icon" :class="{ 'comment__answers-icon--open': repliesOpen }" aria-hidden="true"><use href="#dropdown" /></svg>
        </button>
    </article>
</template>
