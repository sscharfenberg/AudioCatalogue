<script setup lang="ts">
import PlayerNavigation from "Components/Player/PlayerNavigation.vue";
defineProps({
    song: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <header class="details-title">
        <div class="details-title__title">
            <h3>{{ song.name }}</h3>
            <player-navigation :nav="song.nav" />
        </div>
        <img v-if="song.cover && song.cover.length === 40" :src="`/storage/${song.cover}`" :alt="song.album" />
    </header>
</template>
