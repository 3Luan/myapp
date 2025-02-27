import { createRouter, createWebHistory } from "vue-router";
import admin from "./admin";
import user from "./user";
import store from "../store";

const routes = [
    ...admin, ...user
];

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Middleware kiểm tra đăng nhập
router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem("token");

    if (token == "undefined") {
        store.dispatch("auth/logout");
        next("/admin/login");
        return;
    }

    if (token && !store.getters["auth/user"]) {
        try {
            await store.dispatch("auth/getProfile");
        } catch (error) {
            store.dispatch("auth/logout");
            next("/admin/login");
            return;
        }
    }

    if (to.meta.requiresAuth && !store.getters["auth/isAuthenticated"]) {
        next("/admin/login");
    } else if (to.path === "/admin/login" && store.getters["auth/isAuthenticated"]) {
        next({ path: "/admin/users", replace: true });
    } else {
        next();
    }
});

export default router;