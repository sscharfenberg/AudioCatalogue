<script setup lang="ts">
import { formatBytes, formatDecimals, formatSeconds } from "@/formatters/numbers";
import AppIcon from "Components/AppIcon/AppIcon.vue";
import { format } from "date-fns";
defineProps({
    song: {
        type: Object,
        required: true
    }
});
</script>
<template>
    <ul class="details-metadata">
        <li v-if="song.artist.name" class="details-link highlight" v-tippy="{ content: 'Artist' }">
            <router-link :to="{ name: 'artist', params: { id: song.artist.encodedName } }">
                <app-icon name="artist" />
                {{ song.artist.name }}
            </router-link>
        </li>
        <li v-if="song.album.name" class="details-link highlight" v-tippy="{ content: 'Album' }">
            <router-link :to="{ name: 'album', params: { id: song.album.id } }">
                <app-icon name="album" />
                <span v-if="song.album.year">[{{ song.album.year }}]</span>
                {{ song.album.name }}
            </router-link>
        </li>
        <li v-if="song.genre.name" class="details-link" v-tippy="{ content: 'Genre' }">
            <router-link :to="{ name: 'genre', params: { id: song.genre.encodedName } }">
                <app-icon name="genre" />
                {{ song.genre.name }}
            </router-link>
        </li>
        <li v-if="song.disc && song.album.discs > 1" v-tippy="{ content: 'Disc' }">
            <app-icon name="album" />
            {{ song.disc }}/{{ song.album.discs }}
        </li>
        <li v-if="song.track" v-tippy="{ content: 'Track' }">
            <app-icon name="track" />
            <span v-if="song.track <= song.album.discTracks">{{ song.track }}/{{ song.album.discTracks }}</span>
            <span v-else>{{ song.track }}</span>
        </li>
        <li v-if="song.duration" v-tippy="{ content: 'Laufzeit' }">
            <app-icon name="time" />
            {{ formatSeconds(song.duration) }}
        </li>
        <li v-if="song.codec" v-tippy="{ content: 'Codec' }">{{ song.codec }}</li>
        <li v-if="song.bitRate" v-tippy="{ content: 'Bit Rate' }">
            <app-icon name="bit_rate" />
            {{ formatBytes(song.bitRate, true) }}/s
        </li>
        <li v-if="song.sampleRate" v-tippy="{ content: 'Sample Rate' }">
            <app-icon name="sample_rate" />
            {{ formatDecimals(song.sampleRate) }} Hz
        </li>
        <li v-if="song.channel" v-tippy="{ content: 'Channel' }">
            <app-icon name="channel" />
            {{ song.channel }}
        </li>
        <li v-if="song.track && song.album.tracks" v-tippy="{ content: 'Dateigröße' }">
            <app-icon name="file" />
            {{ formatBytes(song.size) }}
            <a
                :href="`/storage/${song.mp3Path}`"
                class="btn default short"
                style="margin-left: auto"
                :download="`${song.artist.name} - ${song.name}.mp3`"
            >
                <app-icon name="download" />
            </a>
        </li>
        <li v-if="song.modifiedAt" v-tippy="{ content: 'Zuletzt geändert' }">
            <app-icon name="datetime" />
            {{ format(song.modifiedAt, "dd.MM.yyyy HH:mm:ss") }}
        </li>
        <li v-if="song.composer" v-tippy="{ content: 'Komponist' }">
            <app-icon name="composer" />
            {{ song.composer }}
        </li>
        <li v-if="song.publisher" v-tippy="{ content: 'Publisher' }">
            <app-icon name="publisher" />
            {{ song.publisher }}
        </li>
        <li v-if="song.path" class="path" v-tippy="{ content: 'Pfad auf Samba Share' }">
            <app-icon name="location" />
            {{ song.path }}
        </li>
    </ul>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/typography" as t;

.path {
    grid-column: 1/-1;

    font-family: map.get(t.$main, "path");
}
</style>
