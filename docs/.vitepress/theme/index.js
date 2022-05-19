import DefaultTheme from "vitepress/theme";
import {Tabs, Tab} from 'vue3-tabs-component';
import Codeblock from "./components/Codeblock.vue";
import "./tabs.css";
import "./custom.css";

export default {
    ...DefaultTheme,
    enhanceApp({ app, router, siteData }) {
        app.component('tabs', Tabs);
        app.component('tab', Tab);
        app.component('codeblock', Codeblock);
    },
};
