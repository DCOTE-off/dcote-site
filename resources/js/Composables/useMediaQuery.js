import { onBeforeUnmount, ref } from 'vue';
import { MOBILE_QUERY, REDUCED_MOTION_QUERY } from '../breakpoints';

/**
 * Реактивный matchMedia.
 *
 * Значение читается синхронно в setup, поэтому первая же отрисовка верная —
 * без вспышки десктопной вёрстки на мобиле.
 */
export function useMediaQuery(query) {
    if (typeof window === 'undefined' || typeof window.matchMedia !== 'function') {
        return ref(false);
    }

    const mediaQueryList = window.matchMedia(query);
    const matches = ref(mediaQueryList.matches);
    const onChange = (event) => {
        matches.value = event.matches;
    };

    mediaQueryList.addEventListener('change', onChange);
    onBeforeUnmount(() => mediaQueryList.removeEventListener('change', onChange));

    return matches;
}

export function useIsMobile() {
    return useMediaQuery(MOBILE_QUERY);
}

export function usePrefersReducedMotion() {
    return useMediaQuery(REDUCED_MOTION_QUERY);
}
