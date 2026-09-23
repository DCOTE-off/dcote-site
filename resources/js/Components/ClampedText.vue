<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, useId, watch } from 'vue';
import '../../css/components/clamped-text.css';

const props = defineProps({
    text: {
        type: String,
        required: true,
    },
    lines: {
        type: Number,
        default: 4,
    },
    expandable: {
        type: Boolean,
        default: true,
    },
    alwaysExpanded: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:expanded']);

const expanded = ref(false);
const isTruncated = ref(false);
const bodyId = useId();

const rootEl = ref(null);
const bodyEl = ref(null);
const contentEl = ref(null);
const ellipsisEl = ref(null);
const buttonEl = ref(null);

const showButton = computed(() => props.expandable && !props.alwaysExpanded);
const isClamped = computed(() => isTruncated.value && !expanded.value && !props.alwaysExpanded);

let resizeObserver = null;
let frame = 0;
let lastWidth = -1;

function getTokens() {
    return props.text.match(/\S+|\s+/g) ?? [];
}

function buildText(tokens, wordCount) {
    if (wordCount <= 0) {
        return '';
    }

    let remaining = wordCount;
    let result = '';

    for (const token of tokens) {
        result += token;

        if (/\S/.test(token)) {
            remaining -= 1;

            if (remaining === 0) {
                return result;
            }
        }
    }

    return result;
}

function fits(el) {
    return el.scrollHeight <= el.clientHeight + 1;
}

function setEllipsis(visible) {
    if (ellipsisEl.value) {
        ellipsisEl.value.style.display = visible ? '' : 'none';
    }
}

function setButton(visible) {
    if (buttonEl.value) {
        buttonEl.value.style.display = visible ? '' : 'none';
    }
}

function applyLayout() {
    const el = bodyEl.value;
    const content = contentEl.value;

    if (!el || !content) {
        return;
    }

    content.textContent = '';
    setEllipsis(false);
    setButton(false);

    if (props.alwaysExpanded) {
        el.style.maxHeight = 'none';
        content.textContent = props.text;
        isTruncated.value = false;
        return;
    }

    const computedStyle = getComputedStyle(el);
    let lineHeight = parseFloat(computedStyle.lineHeight);
    if (!Number.isFinite(lineHeight) || lineHeight <= 0) {
        lineHeight = parseFloat(computedStyle.fontSize) * 1.2;
    }

    el.style.maxHeight = lineHeight > 0 ? `${lineHeight * props.lines}px` : '';

    if (expanded.value) {
        el.style.maxHeight = 'none';
        content.textContent = props.text;
        isTruncated.value = false;
        setButton(showButton.value);
        return;
    }

    content.textContent = props.text;
    if (fits(el)) {
        isTruncated.value = false;
        return;
    }

    setEllipsis(true);
    setButton(showButton.value);

    const tokens = getTokens();
    const wordCount = tokens.reduce((total, token) => total + (/\S/.test(token) ? 1 : 0), 0);

    if (!wordCount) {
        isTruncated.value = false;
        return;
    }

    let low = 0;
    let high = wordCount - 1;
    let best = 0;

    while (low <= high) {
        const mid = (low + high) >> 1;
        content.textContent = buildText(tokens, mid + 1);

        if (fits(el)) {
            best = mid + 1;
            low = mid + 1;
        } else {
            high = mid - 1;
        }
    }

    content.textContent = buildText(tokens, best);
    isTruncated.value = true;
}

function layout() {
    applyLayout();
    rootEl.value?.setAttribute('data-ready', '');
}

function scheduleLayout() {
    if (frame) {
        cancelAnimationFrame(frame);
    }

    frame = requestAnimationFrame(() => {
        frame = 0;
        layout();
    });
}

function toggle() {
    if (props.alwaysExpanded) {
        return;
    }

    expanded.value = !expanded.value;
    emit('update:expanded', expanded.value);
}

function onResize(entries) {
    const width = entries[0]?.contentRect.width ?? 0;

    if (Math.abs(width - lastWidth) < 1) {
        return;
    }

    lastWidth = width;
    scheduleLayout();
}

watch(expanded, () => nextTick(scheduleLayout));

watch(
    () => [props.text, props.lines, props.alwaysExpanded],
    () => nextTick(scheduleLayout),
);

onMounted(() => {
    layout();

    lastWidth = bodyEl.value?.getBoundingClientRect().width ?? -1;

    if (typeof ResizeObserver === 'function' && bodyEl.value) {
        resizeObserver = new ResizeObserver(onResize);
        resizeObserver.observe(bodyEl.value);
    }

    if (document.fonts?.ready) {
        document.fonts.ready.then(scheduleLayout);
    }
});

onBeforeUnmount(() => {
    if (resizeObserver) {
        resizeObserver.disconnect();
    }

    if (frame) {
        cancelAnimationFrame(frame);
    }
});

defineExpose({ toggle, expanded, isTruncated });
</script>

<template>
    <div ref="rootEl" class="clamped-text">
        <p :id="bodyId" ref="bodyEl" class="clamped-text__body" :class="{ 'is-expanded': expanded }">
            <span ref="contentEl" class="clamped-text__content" :aria-hidden="isClamped ? 'true' : undefined"></span
            ><span ref="ellipsisEl" class="clamped-text__ellipsis" aria-hidden="true">…</span
            ><span v-if="showButton" ref="buttonEl" class="clamped-text__button"
                ><slot :id="bodyId" name="button" :toggle="toggle" :expanded="expanded"
            /></span>
        </p>
        <span v-if="isClamped" class="clamped-text__sr">{{ text }}</span>
    </div>
</template>
