import { onBeforeUnmount, onMounted, ref } from 'vue';

/**
 * Отслеживает, переполняется ли горизонтальный контейнер по горизонтали.
 * Возвращает isScrollable — вешается классом, чтобы добавить отступ под скроллбар
 * только когда он есть (паттерн из Breadcrumbs.vue).
 */
export function useScrollable(elementRef) {
    const isScrollable = ref(false);

    function check() {
        const el = elementRef.value;
        isScrollable.value = !!el && el.scrollWidth > el.clientWidth;
    }

    onMounted(() => {
        check();
        window.addEventListener('resize', check);
    });

    onBeforeUnmount(() => window.removeEventListener('resize', check));

    return { isScrollable, check };
}