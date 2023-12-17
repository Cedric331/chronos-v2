import './bootstrap';
import '../css/app.css';
import i18n from './i18n';
import { plugin as VueTippy } from 'vue-tippy'
import 'tippy.js/dist/tippy.css'
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import dateFormatPlugin from './Plugin/date-format-plugin';
import Notifications from '@kyvg/vue3-notification'
import store from './store';
import '@fortawesome/fontawesome-free/css/all.css';


const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(VueTippy)
            .use(store)
            .use(i18n)
            .use(Notifications)
            .use(ZiggyVue, Ziggy)
            .use(dateFormatPlugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
