const admin = [
    {
        path: "/admin",
        component: () => import("../layouts/admin.vue"),
        children: [
            {
                path: "users",
                name: "admin-users",
                component: () => import("../pages/admin/users/index.vue"),
                meta: { requiresAuth: true, isAdmin: true },
            },
            {
                path: "roles",
                name: "admin-roles",
                component: () => import("../pages/admin/roles/index.vue"),
                meta: { requiresAuth: true, isAdmin: true },
            },
            {
                path: "products",
                name: "admin-products",
                component: () => import("../pages/admin/products/index.vue"),
                meta: { requiresAuth: true, isAdmin: true },
            },
            {
                path: "orders",
                name: "admin-orders",
                component: () => import("../pages/admin/order/index.vue"),
                meta: { requiresAuth: true, isAdmin: true },
            },
        ]
    },
    {
        path: "/admin/login", // Định nghĩa route login cho admin
        name: "admin-login",
        component: () => import("../pages/admin/login/index.vue"),
        meta: { layout: 'none' } // Thêm meta để xác định trang không dùng layout
    }
];

export default admin;
