import './bootstrap';
import '../css/app.css';
import 'vue3-toastify/dist/index.css';
import VueAnimateOnScroll from 'vue-animate-onscroll';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import DateFormat from '@voidsolutions/vue-dateformat';
import('preline');
import Helper from './Helpers/Helper.js'
import Antd from "ant-design-vue";
import 'ant-design-vue/dist/reset.css';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import VueSidebarMenu from 'vue-sidebar-menu'
import 'vue-sidebar-menu/dist/vue-sidebar-menu.css'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Helper)
            .use(ZiggyVue, Ziggy)
            .use(DateFormat)
            .use(VueAnimateOnScroll)
            .use(Antd)
            .use('VueDatePicker', VueDatePicker)
            .mount(el);
    },
    progress: {
        color: '#008083',
        showSpinner: false,
    },
});
