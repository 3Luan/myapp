const admin = [
    {
        path: "/admin",
        component: () => import("@/layouts/admin.vue"),
        children: [
            {
                path: "users",
                name: "admin-users",
                component: () => import("@/pages/admin/users/index.vue"),
                meta: { requiresAuth: true, isAdmin: true },
            },
            {
                path: "roles",
                name: "admin-roles",
                component: () => import("@/pages/admin/roles/index.vue"),
                meta: { requiresAuth: true, isAdmin: true },
            },
            {
                path: "products",
                name: "admin-products",
                component: () => import("@/pages/admin/products/index.vue"),
                meta: { requiresAuth: true, isAdmin: true },
            },
            {
                path: "orders",
                name: "admin-orders",
                component: () => import("@/pages/admin/order/index.vue"),
                meta: { requiresAuth: true, isAdmin: true },
            },
            {
                path: "discounts",
                name: "admin-discounts",
                component: () => import("@/pages/admin/discount/index.vue"),
                meta: { requiresAuth: true, isAdmin: true },
            },
        ]
    },
    {
        path: "/admin/login",
        name: "admin-login",
        component: () => import("@/pages/admin/login/index.vue"),
        meta: { layout: 'none' } 
    }
];

export default admin;
