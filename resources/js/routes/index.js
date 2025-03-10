import { createRouter, createWebHistory } from "vue-router";
import admin from "./admin";
import user from "./user";
import store from "../store";
import NotFound from "../components/NotFound.vue";

const routes = [
    ...admin, ...user,
    { path: "/:pathMatch(.*)*", name: "NotFound", component: NotFound }
];

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach(async (to, from, next) => {
    const token_admin = localStorage.getItem("token_admin");
    const token_user = localStorage.getItem("token");

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

    // Check access
    if (to.meta.requiresAuth) {
        if (to.meta.isAdmin && !store.getters["authAdmin/isAuthenticated"]) {
            next("/admin/login"); // If admin page but not logged in admin
        } else if (!to.meta.isAdmin && !store.getters["auth/isAuthenticated"]) {
            next("/login"); // If the user page but not logged in user
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

    // Load data
    // Load Cart
    if (!store.getters["cart/cart"] && token_user) {
        await store.dispatch("cart/getCarts", {
          search: "",
          currentPage: 1,
          limit: 10
        });
    }
});


export default router;