import "./bootstrap";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { createPinia } from "pinia";
import { VueQueryPlugin } from "@tanstack/vue-query";
import http from "./utils/http";
import auth from "./plugins/auth";
import "vue-toast-notification/dist/theme-bootstrap.css";
import ToastPlugin from "vue-toast-notification";
import "vue-select/dist/vue-select.css";
import VueSelect from "vue-select";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
const pinia = createPinia();
createApp(App)
    .use(http)
    .use(pinia)
    .use(router)
    .use(VueQueryPlugin)
    .use(auth)
    .use(ToastPlugin)
    .component("v-select", VueSelect)
    .component("VueDatePicker", VueDatePicker)
    .mount("#__sip__point__base__app");
