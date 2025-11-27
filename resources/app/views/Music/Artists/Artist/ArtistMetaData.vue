<script setup lang="ts">
import { formatBytes, formatDecimals, formatSeconds } from "@/formatters/numbers";
import AppIcon from "Components/AppIcon/AppIcon.vue";
defineProps({
    data: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <ul class="details-metadata short">
        <li v-if="data.numSongs" v-tippy="{ content: 'Songs' }">
            <app-icon name="music" />
            {{ formatDecimals(data.numSongs) }}
        </li>
        <li v-if="data.numAlbums" v-tippy="{ content: 'Alben' }">
            <app-icon name="album" />
            {{ formatDecimals(data.numAlbums) }}
        </li>
        <li v-if="data.songsDuration" v-tippy="{ content: 'Laufzeit' }">
            <app-icon name="time" />
            {{ formatSeconds(data.songsDuration) }}
        </li>
        <li v-if="data.songsFileSize" v-tippy="{ content: 'Dateigröße' }">
            <app-icon name="time" />
            {{ formatBytes(data.songsFileSize) }}
        </li>
    </ul>
</template>
