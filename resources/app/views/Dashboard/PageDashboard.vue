<script setup lang="ts">
import WidgetAlbums from "Components/Widgets/WidgetAlbums.vue";
import WidgetArtists from "Components/Widgets/WidgetArtists.vue";
import WidgetAudiobooks from "Components/Widgets/WidgetAudiobooks.vue";
import WidgetGenres from "Components/Widgets/WidgetGenres.vue";
import WidgetGlobal from "Components/Widgets/WidgetGlobal.vue";
import WidgetSongs from "Components/Widgets/WidgetSongs.vue";
</script>

<template>
    <div class="widget-grid">
        <widget-global />
        <widget-genres />
        <widget-artists />
        <widget-albums />
        <widget-songs />
        <widget-audiobooks />
    </div>
</template>
