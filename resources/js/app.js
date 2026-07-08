import './bootstrap';
import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';

createInertiaApp({
    title: (titulo) => titulo ? `${titulo} - Autoescuela America` : 'Autoescuela America',
    resolve: (nombre) => {
        const paginas = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return paginas[`./Pages/${nombre}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#0f766e',
    },
});
