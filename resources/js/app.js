import './bootstrap';
import '../css/app.css';
import Antd from "ant-design-vue";
import "ant-design-vue/dist/antd.css";
import 'vue3-toastify/dist/index.css';
import Vue3Toastify from 'vue3-toastify';
import VueCreditCardValidation from 'vue-credit-card-validation';
import VueAnimateOnScroll from 'vue-animate-onscroll';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
//import VueGeolocation from 'vue-browser-geolocation';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(Antd)
            .use(VueCreditCardValidation)
            .use(VueAnimateOnScroll)
            //.use(VueGeolocation)
            .use(Vue3Toastify, {
                autoClose: 3000,
            })
            .mount(el);
    },
    progress: {
        color: '#008083',
    },
});
