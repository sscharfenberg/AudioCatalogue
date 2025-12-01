<script setup lang="ts">
import AppHeaderNavigationItem from "./AppHeaderNavigationItem.vue";
const items = [
    { name: "dashboard", label: "Dashboard", icon: "home" },
    { name: "music", label: "Musik", icon: "music" },
    { name: "audiobooks", label: "Audiobooks", icon: "audiobooks" },
    { name: "playlists", label: "Playlists", icon: "playlist" }
];
</script>

<template>
    <nav>
        <app-header-navigation-item
            v-for="item in items"
            :key="item.name"
            :icon="item.icon"
            :label="item.label"
            :name="item.name"
        />
    </nav>
</template>

<style scoped lang="scss">
nav {
    display: grid;
    grid-template-columns: 1fr 1fr;

    margin-left: auto;

    gap: 8px;
}
</style>
