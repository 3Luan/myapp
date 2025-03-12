import { createRouter, createWebHistory } from "vue-router";
import adminRoutes from "./admin";
import userRoutes from "./user";
import store from "../store";
import NotFound from "../components/NotFound.vue";

const routes = [
    ...adminRoutes,
    ...userRoutes,
    { path: "/:pathMatch(.*)*", name: "NotFound", component: NotFound }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

const isAdminPath = (path) => path.startsWith('/admin');

router.beforeEach(async (to, from, next) => {
    const tokenAdmin = localStorage.getItem("token_admin");
    const tokenUser = localStorage.getItem("token");
    const isAdminRoute = isAdminPath(to.path);

    // Handle authentication
    const handleAuth = async (token, getter, profileAction, logoutAction, loginPath) => {
        if (token && token !== "undefined" && !store.getters[getter]) {
            try {
                await store.dispatch(profileAction);
            } catch (error) {
                store.dispatch(logoutAction);
                return next(loginPath);
            }
        }
        return null;
    };

    // Admin authentication
    if (isAdminRoute) {
        const redirect = await handleAuth(
            tokenAdmin,
            "authAdmin/admin",
            "authAdmin/getProfileAdmin",
            "authAdmin/logoutAdmin",
            "/admin/login"
        );
        if (redirect) return redirect;
    }

    // User authentication
    else {
        const redirect = await handleAuth(
            tokenUser,
            "auth/user",
            "auth/getProfile",
            "auth/logout",
            "/login"
        );
        if (redirect) return redirect;
    }

    // Check access
    if (to.meta.requiresAuth) {
        const isAuthenticated = isAdminRoute 
            ? store.getters["authAdmin/isAuthenticated"]
            : store.getters["auth/isAuthenticated"];
        
        if (!isAuthenticated) {
            return next(isAdminRoute ? "/admin/login" : "/login");
        }
    }

    // Redirect if already authenticated
    if (to.path === "/admin/login" && store.getters["authAdmin/isAuthenticated"]) {
        return next({ path: "/admin/users", replace: true });
    }
    if (to.path === "/login" && store.getters["auth/isAuthenticated"]) {
        return next({ path: "/", replace: true });
    }

    // Load data cart
    if (!isAdminRoute && tokenUser && !store.getters["cart/cart"]) {
        await store.dispatch("cart/getCarts", {
            search: "",
            currentPage: 1,
            limit: 10
        });
    }

    next();
});

export default router;