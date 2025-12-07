<script setup lang="ts">
import PlayerAudiobookNavigation from "./PlayerAudiobookNavigation.vue";
const emit = defineEmits(["play"]);
const props = defineProps({
    title: {
        type: String,
        required: true
    },
    tracks: {
        type: Array,
        required: true
    },
    cover: String,
    bookEncodedName: {
        type: String,
        required: true
    }
});
const onPlay = (value: string) => {
    emit("play", value);
};
const pageTitle = () => {
    if (props.title.length < 22) return props.title;
    return props.title.substring(0, 19) + "...";
};
</script>

<template>
    <header class="details-title">
        <div class="details-title__title">
            <h3>{{ title }}</h3>
            <player-audiobook-navigation :tracks="tracks" :book-encoded-name="bookEncodedName" @play="onPlay" />
        </div>
        <Teleport to="#specificTitle">: {{ pageTitle() }}</Teleport>
        <img v-if="cover && cover.length > 48" :src="cover" :alt="title" />
        <img v-else src="./missing-cover.jpg" alt="Cover fehlt!" v-tippy="{ content: 'Cover fehlt!' }" />
    </header>
</template>
