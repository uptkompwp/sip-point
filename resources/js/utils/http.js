import axios from "axios";
const http = axios.create({
    baseURL: "/api/",
});

http.defaults.headers.common["API_KEY"] = import.meta.env.VITE_API_SECRET;

export default (app) => {
    app.axios = http;
    app.$http = http;
    app.config.globalProperties.axios = http;
    app.config.globalProperties.$http = http;
};
