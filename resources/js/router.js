import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: () => import("./Pages/HomeRoute.vue"),
    },
    {
        path: "/kviz-:id", // Dynamic route for events
        component: () => import("./Pages/HomeRoute.vue"),
        name: "event",
    },
    {
        path: "/vysledky",
        component: () => import("./Pages/ResultsRoute.vue"),
    },
    {
        path: "/tymy",
        component: () => import("./Pages/TeamsRoute.vue"),
    },
    {
        path: "/registrace",
        component: () => import("./Pages/RegistrationRoute.vue"),
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
