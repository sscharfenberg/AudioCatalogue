<script setup lang="ts">
import "vidstack/bundle";
import type { MediaPlayerElement } from "vidstack/elements";
import { ref } from "vue";
defineProps({
    src: String,
    title: String,
    autoplay: {
        type: Boolean,
        default: false
    }
});
const $player = ref<MediaPlayerElement>();
</script>

<template>
    <media-player
        :title="title"
        :src="src"
        :autoplay="autoplay ? true : null"
        ref="$player"
        @ended="$emit('player-ended')"
    >
        <media-provider></media-provider>
        <media-audio-layout></media-audio-layout>
    </media-player>
</template>
