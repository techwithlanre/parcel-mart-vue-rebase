import './bootstrap';
import '../css/app.css';
import 'vue3-toastify/dist/index.css';
import Vue3Toastify from 'vue3-toastify';
import VueAnimateOnScroll from 'vue-animate-onscroll';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import TawkMessengerVue from '@tawk.to/tawk-messenger-vue-3';
import DateFormat from '@voidsolutions/vue-dateformat';
import('preline');
import Antd from "ant-design-vue";
import 'ant-design-vue/dist/reset.css';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(DateFormat)
            .use(VueAnimateOnScroll)
            .use(Antd)
            /*.use(TawkMessengerVue, {
                propertyId : '60c6342465b7290ac635bafa',
                widgetId : '1f833nce0'
            })*/
            .mount(el);
    },
    progress: {
        color: '#008083',
    },
});
