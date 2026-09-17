import { reactive } from 'vue';

export const toastState = reactive({
    toasts: [],
    _id: 0,
});

export function pushToast(type, message) {
    const id = ++toastState._id;
    toastState.toasts.push({ id, type, message });

    setTimeout(() => dismissToast(id), 4000);
}

export function dismissToast(id) {
    toastState.toasts = toastState.toasts.filter((t) => t.id !== id);
}
