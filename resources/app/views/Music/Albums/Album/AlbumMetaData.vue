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
    <ul class="details-metadata">
        <li v-if="data.year" v-tippy="{ content: 'Erscheinungsjahr' }">
            <app-icon name="date" />
            {{ data.year }}
        </li>
        <li v-if="data.artist?.name" v-tippy="{ content: 'Artist' }" class="highlight details-link">
            <router-link :to="{ name: 'artist', params: { id: data.artist.encodedName } }">
                <app-icon name="artist" />
                {{ data.artist.name }}
            </router-link>
        </li>
        <li v-if="data.genre?.name" class="details-link" v-tippy="{ content: 'Genre' }">
            <router-link :to="{ name: 'genre', params: { id: data.genre.encodedName } }">
                <app-icon name="genre" />
                {{ data.genre.name }}
            </router-link>
        </li>
        <li v-if="data.duration" v-tippy="{ content: 'Gesamte Laufzeit' }">
            <app-icon name="time" />
            {{ formatSeconds(data.duration) }}
        </li>
        <li v-if="data.discs" v-tippy="{ content: 'Discs' }">
            <app-icon name="album" />
            {{ data.discs }}
        </li>
        <li v-if="data.numSongs" v-tippy="{ content: 'Anzahl Songs' }">
            <app-icon name="music" />
            {{ formatDecimals(data.numSongs) }}
        </li>
        <li v-if="data.fileSize" v-tippy="{ content: 'Dateigröße gesamt' }">
            <app-icon name="file" />
            {{ formatBytes(data.fileSize) }}
        </li>
        <li v-if="data.downloadLink">
            <a :href="data.downloadLink" class="btn primary">
                <app-icon name="download" />
                Download Album
            </a>
        </li>
    </ul>
</template>
