import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: () => import("./Pages/HomeRoute.vue"),
    },
    {
        path: "/kviz",
        component: () => import("./Pages/ResultsRoute.vue"),
    },
    {
        path: "/kviz-:id", // Dynamic route for events
        component: () => import("./Pages/ResultsRoute.vue"),
    },
    {
        path: "/kalendar",
        component: () => import("./Pages/CalendarRoute.vue"),
    },
    {
        path: "/tymy",
        component: () => import("./Pages/TeamsRoute.vue"),
    },
    {
        path: "/tymy/:id",
        component: () => import("./Pages/TeamRoute.vue"),
    },
    {
        path: "/registrace-:id",
        component: () => import("./Pages/RegistrationRoute.vue"),
    },
    {
        path: "/galerie",
        component: () => import("./Pages/GalleryRoute.vue"),
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        return savedPosition || { top: 0, behavior: 'smooth' };
    },
});
