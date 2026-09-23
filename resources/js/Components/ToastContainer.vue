<script setup>
import { toastState, dismissToast } from '../stores/toast';
import '../../css/components/toast.css';
</script>

<template>
    <TransitionGroup name="toast" tag="div" class="toast-stack">
        <div v-for="t in toastState.toasts" :key="t.id" class="toast" :class="`toast--${t.type}`">
            <h1>{{ t.type === 'success' ? 'УСПЕХ!' : 'ОШИБКА!' }}</h1>
            <span>{{ t.message }}</span>
        </div>
    </TransitionGroup>
</template>
