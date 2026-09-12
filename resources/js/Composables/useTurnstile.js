import { onMounted, onBeforeUnmount, ref } from 'vue';

const TURNSTILE_SRC = 'https://challenges.cloudflare.com/turnstile/v0/api.js';
let scriptPromise = null;

function loadTurnstileScript() {
    if (window.turnstile) return Promise.resolve();
    if (scriptPromise) return scriptPromise;

    scriptPromise = new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = TURNSTILE_SRC;
        script.async = true;
        script.defer = true;
        script.onload = () => {
            if (window.turnstile) {
                resolve();
            } else {
                reject(new Error('Turnstile script loaded without window.turnstile'));
            }
        };
        script.onerror = () => reject(new Error('Failed to load Turnstile script'));
        document.head.appendChild(script);
    });

    return scriptPromise;
}

export function useTurnstile({ siteKey, onSuccess }) {
    const container = ref(null);
    const ready = ref(!siteKey);
    let widgetId = null;

    onMounted(async () => {
        try {
            await loadTurnstileScript();
            if (!siteKey || !container.value) return;

            widgetId = window.turnstile.render(container.value, {
                sitekey: siteKey,
                callback: (token) => {
                    ready.value = true;
                    onSuccess?.(token);
                },
            });
        } catch (err) {
            ready.value = true;
        }
    });

    onBeforeUnmount(() => {
        if (widgetId !== null && window.turnstile) {
            window.turnstile.remove(widgetId);
            widgetId = null;
        }
    });

    return { container, ready };
}
