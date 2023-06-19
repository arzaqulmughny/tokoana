import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import "../css/app.scss";
import VueApexCharts from "vue3-apexcharts";

const app = createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .component("apexchart", VueApexCharts)
            .mount(el);
    }
});
