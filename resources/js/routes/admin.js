const admin = [
    {
        path: "/admin",
        component: () => import("../layouts/admin.vue"),
        children: [
            {
                path: "users",
                name: "admin-users",
                component: () => import("../pages/admin/users/index.vue"),
                meta: { requiresAuth: true }
            },
            {
                path: "roles",
                name: "admin-roles",
                component: () => import("../pages/admin/roles/index.vue")
            },
            {
                path: "settings",
                name: "admin-settings",
                component: () => import("../pages/admin/settings/index.vue")
            }
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
