import PageDashboard from "../../views/Dashboard/PageDashboard.vue";
import { audiobookRoutes } from "./audiobooks";
import { musicRoutes } from "./music";

export const routes = [
    {
        path: "/",
        component: PageDashboard,
        name: "dashboard",
        meta: { title: "Dashboard", icon: "dashboard" }
    },
    ...musicRoutes,
    ...audiobookRoutes,
    {
        path: "/playlists",
        component: () => import("../../views/Playlists/PagePlaylists.vue"),
        name: "playlists",
        meta: { title: "Playlists", icon: "playlist" }
    }
];
