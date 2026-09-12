import './bootstrap';
import '../css/app.css';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

// Заголовок серверно рендерится в app.blade.php; при SPA-переходах обновляем вручную.
router.on('navigate', (event) => {
    const title = event.detail.page.props.meta?.title;

    if (title) {
        document.title = title;
    }
});

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });

        return pages[`./Pages/${name}.vue`].default;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
});
