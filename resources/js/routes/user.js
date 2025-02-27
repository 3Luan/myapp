const admin = [
    {
        path: "/",
        component: () => import("../layouts/user.vue"),
        children: [
            {
                path: "/",
                name: "home",
                component: () => import("../pages/home/index.vue"),
                // meta: { requiresAuth: true }
            },
        ]
    },
    {
        path: "/register",
        name: "register",
        component: () => import("../pages/register/index.vue"),
        meta: { layout: 'none' }
    }
];

export default admin;
