<script setup>
import { computed, nextTick, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useIsMobile } from '../Composables/useMediaQuery';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    replyingTo: {
        type: String,
        default: null,
    },
    placeholder: {
        type: String,
        default: 'Начните писать комментарий...',
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'submit', 'cancel']);

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const isMobile = useIsMobile();

const fieldEl = ref(null);

const value = computed({
    get: () => props.modelValue,
    set: (next) => emit('update:modelValue', next),
});

onMounted(() => {
    if (!props.autofocus || !isAuthenticated.value || !fieldEl.value) {
        return;
    }

    const el = fieldEl.value;
    el.focus();
    el.setSelectionRange(el.value.length, el.value.length);
});

function wrap(marker, placeholder = 'текст') {
    const el = fieldEl.value;
    if (!el) {
        return;
    }

    const start = el.selectionStart;
    const end = el.selectionEnd;
    const selected = el.value.slice(start, end) || placeholder;
    const wrapped = `${marker}${selected}${marker}`;

    value.value = el.value.slice(0, start) + wrapped + el.value.slice(end);

    nextTick(() => {
        el.focus();
        const caret = start + marker.length + selected.length;
        el.setSelectionRange(caret, caret);
    });
}
</script>

<template>
    <div class="comments__input">
        <div v-if="replyingTo" class="comment-input__reply-to">
            <span>Ответ на «{{ replyingTo }}»</span>
        </div>
        <div class="comment-input__edit">
            <button
                type="button"
                class="comment-input__edit-btn"
                aria-label="Жирный"
                @click="wrap('**', 'жирный текст')">
                <svg
                    class="comment-input__edit-icon"
                    viewBox="0 0 20 20"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10.64 8.12C10.89 7.9 11.02 7.58 11.02 7.17C11.02 6.76 10.9 6.45 10.65 6.23C10.4 6.01 10.07 5.91 9.65 5.91H8.25V8.45H9.67C10.07 8.45 10.39 8.34 10.64 8.12Z"
                        fill="currentColor" />
                    <path
                        d="M17 0H3C1.34 0 0 1.34 0 3V17C0 18.66 1.34 20 3 20H17C18.66 20 20 18.66 20 17V3C20 1.34 18.66 0 17 0ZM13.67 15.18C12.88 15.88 11.83 16.22 10.51 16.22H6.8C6.42 16.22 6.09 16.09 5.83 15.82C5.57 15.56 5.43 15.23 5.43 14.85V4.78C5.43 4.4 5.56 4.07 5.83 3.81C6.09 3.55 6.42 3.41 6.8 3.41H10.27C11.3 3.41 12.15 3.71 12.82 4.32C13.49 4.93 13.82 5.73 13.82 6.74C13.82 7.28 13.68 7.76 13.41 8.19C13.13 8.62 12.74 8.94 12.24 9.17V9.22C13.05 9.4 13.7 9.76 14.16 10.29C14.62 10.82 14.85 11.52 14.85 12.37C14.85 13.55 14.46 14.48 13.67 15.18Z"
                        fill="currentColor" />
                    <path
                        d="M11.44 11.3C11.1 11.05 10.67 10.93 10.15 10.93H8.24V13.72H10.17C10.72 13.72 11.16 13.6 11.47 13.36C11.78 13.12 11.95 12.78 11.95 12.33C11.95 11.88 11.78 11.54 11.44 11.3Z"
                        fill="currentColor" />
                </svg>
            </button>
            <button
                type="button"
                class="comment-input__edit-btn"
                aria-label="Курсив"
                @click="wrap('*', 'курсивный текст')">
                <svg
                    class="comment-input__edit-icon"
                    viewBox="0 0 20 20"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M17 0H3C1.34 0 0 1.34 0 3V17C0 18.66 1.34 20 3 20H17C18.66 20 20 18.66 20 17V3C20 1.34 18.66 0 17 0ZM13.3 5.89L8.44 15.36C8.14 15.95 7.89 16.33 7.7 16.51C7.51 16.69 7.29 16.78 7.05 16.78C6.76 16.78 6.53 16.7 6.36 16.54C6.19 16.38 6.1 16.15 6.1 15.85C6.1 15.6 6.31 15.07 6.73 14.27L11.79 4.44C12.13 3.77 12.5 3.43 12.92 3.43C13.57 3.43 13.89 3.74 13.89 4.37C13.89 4.6 13.69 5.11 13.3 5.9V5.89Z"
                        fill="currentColor" />
                </svg>
            </button>
            <button
                type="button"
                class="comment-input__edit-btn"
                aria-label="Зачёркнутый"
                @click="wrap('~~', 'зачёркнутый текст')">
                <svg
                    class="comment-input__edit-icon"
                    viewBox="0 0 20 20"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M17,0H3C1.34,0,0,1.34,0,3v14c0,1.66,1.34,3,3,3h14c1.66,0,3-1.34,3-3V3c0-1.66-1.34-3-3-3ZM17,11h-2.26c.48.72.76,1.58.76,2.5,0,2.48-2.02,4.5-4.5,4.5h-1.5s-.09,0-.13,0c-1.86,0-4.03-.62-4.76-2.04-.25-.49-.06-1.09.43-1.35.49-.25,1.09-.06,1.35.43.22.42,1.42,1.01,3.1.96h1.52c1.38,0,2.5-1.12,2.5-2.5s-1.12-2.5-2.5-2.5H3c-.55,0-1-.45-1-1s.45-1,1-1h2.26c-.48-.72-.76-1.58-.76-2.5,0-2.48,2.02-4.5,4.5-4.5h1c1.9-.02,4.14.59,4.89,2.04.25.49.06,1.09-.43,1.35-.49.25-1.09.06-1.35-.43-.22-.42-1.4-1-3.1-.96h-1.02c-1.38,0-2.5,1.12-2.5,2.5s1.12,2.5,2.5,2.5h8c.55,0,1,.45,1,1s-.45,1-1,1Z"
                        fill="currentColor" />
                </svg>
            </button>
            <button
                style="margin-left: var(--fs-gap20)"
                type="button"
                class="comment-input__edit-btn"
                aria-label="Спойлер"
                @click="wrap('||', 'скрытый текст')">
                <svg
                    class="comment-input__spoiler-icon"
                    viewBox="0 0 23.8 17.01"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path
                        d="M3.72,2.19c-.5-.5-.5-1.32,0-1.82.5-.5,1.32-.5,1.82,0l1.14,1.09C8.24.6,10.01,0,11.9,0,14.53,0,16.91,1.16,18.82,2.54c1.92,1.38,3.43,3.03,4.35,4.14h0c.41.52.63,1.17.63,1.82s-.22,1.3-.63,1.81h0c-.84,1.02-2.17,2.48-3.86,3.78l.7.72c.5.5.5,1.32,0,1.82s-1.32.5-1.82,0L3.72,2.19ZM15.36,10.14c.24-.5.37-1.05.37-1.64,0-2.11-1.71-3.83-3.83-3.83-.59,0-1.14.13-1.64.37l5.1,5.1ZM.63,6.68c.55-.66,1.31-1.52,2.24-2.39l12.18,12.18c-1,.34-2.05.54-3.15.54-2.63,0-5.01-1.16-6.92-2.54-1.92-1.38-3.43-3.03-4.35-4.14h0c-.41-.52-.63-1.17-.63-1.82s.22-1.3.63-1.81h0v-.02Z"
                        fill="currentColor"
                        fill-rule="evenodd" />
                </svg>
            </button>
            <button
                v-if="isMobile"
                type="button"
                class="comment-input__close-btn"
                aria-label="Закрыть"
                @click="emit('cancel')">
                <img class="comment-input__edit-icon" :src="'/svgs/close.svg'" alt="Закрыть" />
            </button>
        </div>
        <textarea
            ref="fieldEl"
            v-model="value"
            class="comment-input__field"
            rows="3"
            :placeholder="placeholder"
            aria-label="Текст комментария"></textarea>
        <div class="comment-input__actions">
            <button
                v-if="!isMobile"
                type="button"
                class="btn-pill-outline"
                style="border-color: rgba(146, 21, 69, 1)"
                @click="emit('cancel')">
                ОТМЕНИТЬ
            </button>
            <button type="button" class="btn-pill" :disabled="!modelValue.trim()" @click="emit('submit')">
                ОТПРАВИТЬ
            </button>
        </div>

        <div v-if="!isAuthenticated" class="comment-input__overlay">
            <Link :href="route('login')">
                <b><p>Войдите в аккаунт, чтобы оставить комментарий</p></b>
            </Link>
        </div>
    </div>
</template>
