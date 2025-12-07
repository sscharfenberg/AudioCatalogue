<script setup lang="ts">
import { usePlayerStore } from "@/stores/player";
import AppButton from "Components/Button/AppButton.vue";
import AutoplaySwitch from "Components/Player/AutoplaySwitch.vue";
import { getNavigation } from "Views/Audiobooks/Audiobook/useNavigation";
import { computed, onMounted } from "vue";
import AudiobookTracks from "./AudiobookTracks.vue";
const props = defineProps({
    tracks: {
        type: Array,
        required: true
    },
    bookEncodedName: {
        type: String,
        required: true
    }
});
const emit = defineEmits(["play"]);
const store = usePlayerStore();
const currentTrack = computed(() => store.getAudiobookBookmark(props.bookEncodedName));
const playFirst = () => {
    emit("play", props.tracks[0].encodedPath);
};
const playAny = (value: string) => {
    if (value) {
        store.setAudiobookBookmark(props.bookEncodedName, value, 0);
        emit("play", value);
    }
};
const nav = computed(() => getNavigation(props.tracks, currentTrack.value?.trackEncodedPath));
onMounted(() => {
    if (currentTrack.value?.trackEncodedPath) {
        playAny(currentTrack.value.trackEncodedPath);
    }
});
</script>

<template>
    <div class="audiobook-buttons">
        <app-button
            :disabled="nav.prev?.encodedPath ? null : true"
            icon="prev"
            v-tippy="{ content: `${nav.prev?.track} ${nav.prev?.name}` }"
            class="btn default"
            @click="playAny(nav.prev?.encodedPath)"
        />
        <app-button
            v-if="!currentTrack?.trackEncodedPath"
            icon="play"
            class="btn primary"
            v-tippy="{ content: `Von Beginn an abspielen` }"
            @click="playFirst"
        />
        <audiobook-tracks v-else :book-encoded-name="bookEncodedName" :tracks="tracks" @play="playAny" />
        <app-button
            :disabled="nav.next?.encodedPath ? null : true"
            icon="next"
            v-tippy="{ content: `${nav.next?.track} ${nav.next?.name}` }"
            class="btn default"
            @click="playAny(nav.next?.encodedPath)"
        />
        <autoplay-switch />
    </div>
</template>

<style scoped lang="scss">
.audiobook-buttons {
    display: flex;
    align-items: center;

    margin-top: auto;

    gap: 1ch;
}
</style>
