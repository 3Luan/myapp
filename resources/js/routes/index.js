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

router.beforeEach(async (to, from, next) => {
    const token_admin = localStorage.getItem("token_admin");
    const token_user = localStorage.getItem("token");

    console.log(token_admin);
    console.log(token_user);


    // ADMIN
    if ((token_admin || token_admin !== "undefined") && !store.getters["authAdmin/admin"]) {
        try {
            await store.dispatch("authAdmin/getProfileAdmin");
        } catch (error) {
            store.dispatch("authAdmin/logoutAdmin");
            next("/admin/login");
            return;
        }
    }

    // USER
    if ((token_user || token_user !== "undefined") && !store.getters["auth/user"]) {
        try {
            await store.dispatch("auth/getProfile");
        } catch (error) {
            store.dispatch("auth/logout");
            next("/login");
            return;
        }
    }

    // Kiểm tra quyền truy cập
    if (to.meta.requiresAuth) {
        if (to.meta.isAdmin && !store.getters["authAdmin/isAuthenticated"]) {
            next("/admin/login"); // Nếu trang admin nhưng chưa đăng nhập admin
        } else if (!to.meta.isAdmin && !store.getters["auth/isAuthenticated"]) {
            next("/login"); // Nếu trang user nhưng chưa đăng nhập user
        } else {
            next();
        }
    } else if (to.path === "/admin/login" && store.getters["authAdmin/isAuthenticated"]) {
        next({ path: "/admin/users", replace: true });
    } else if (to.path === "/login" && store.getters["auth/isAuthenticated"]) {
        next({ path: "/", replace: true });
    } else {
        next();
    }
});

export default router;