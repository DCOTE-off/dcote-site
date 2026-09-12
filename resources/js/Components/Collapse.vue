<script setup>
import { useId, ref } from 'vue';
import '../../css/components/collapse.css';

const props = defineProps({
    initiallyOpen: {
        type: Boolean,
        default: false,
    },
    alwaysOpen: {
        type: Boolean,
        default: false,
    },
});

const isOpen = ref(props.initiallyOpen);
const contentId = useId();

function open() {
    isOpen.value = true;
}

function close() {
    isOpen.value = false;
}

function toggle() {
    isOpen.value = !isOpen.value;
}

defineExpose({ open, close, toggle, isOpen: () => isOpen.value  });
</script>

<template>
    <div class="collapse">
        <button
            v-if="!alwaysOpen"
            type="button"
            class="collapse__trigger"
            :class="{ 'is-open': isOpen }"
            :aria-expanded="isOpen"
            :aria-controls="contentId"
            @click="toggle">
            <slot name="trigger" />
        </button>
        <template v-else>
            <slot name="trigger" />
        </template>
        <div class="collapse__wrapper" :class="{ 'is-open': alwaysOpen || isOpen }">
            <div class="collapse__content" :id="contentId">
                <slot />
            </div>
        </div>
    </div>
</template>