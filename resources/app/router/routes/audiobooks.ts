export const audiobookRoutes = [
    {
        path: "/audiobooks",
        children: [
            {
                path: "",
                component: () => import("../../views/Audiobooks/PageAudiobooks.vue"),
                name: "audiobooks",
                meta: { title: "Audiobooks", icon: "audiobooks" }
            },
            {
                path: ":id",
                component: () => import("../../views/Audiobooks/Audiobook/PageAudiobook.vue"),
                name: "artist",
                meta: { title: "Audiobook", icon: "audiobook" }
            }
        ]
    }
];
