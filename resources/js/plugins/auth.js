import { createAuth } from "@websanova/vue-auth/src/v3.js";
import driverAuthBearer from "@websanova/vue-auth/src/drivers/auth/bearer.js";
import driverHttpAxios from "@websanova/vue-auth/src/drivers/http/axios.1.x.js";
import driverRouterVueRouter from "@websanova/vue-auth/src/drivers/router/vue-router.2.x";

export default (app) => {
    const auth = createAuth({
        plugins: {
            http: app.axios,
            router: app.router,
        },
        drivers: {
            http: driverHttpAxios,
            auth: driverAuthBearer,
            router: driverRouterVueRouter,
        },
        options: {
            loginData: {
                url: "auth/login",
                method: "POST",
                redirect: { name: "home" },
                fetchUser: true,
                staySignedIn: true,
            },
            logoutData: {
                url: "auth/logout",
                method: "POST",
                redirect: { name: "auth-login" },
            },
            rolesKey: "role",
            fetchData: {
                url: "auth/me",
                method: "GET",
                enabled: true,
                interval: 30,
            },
            refreshData: {
                url: "auth/refresh",
                method: "POST",
            },
            tokenDefaultKey: "sip_point_token_app",
            refreshTokenKey: "sip_point_token_app",
            tokenExpiresKey: "expires_in",
            forbiddenRedirect: { name: "forbidden" },
            notFoundRedirect: { name: "not-found" },
            authRedirect: { name: "auth-login" },
        },
    });
    app.auth = auth;
    app.use(auth);
};
