<script setup lang="ts">
import { router } from "@/router";
import { usePlayerStore } from "@/stores/player";
import axios from "axios";
import ShowError from "Components/Error/ShowError.vue";
import LoadingSpinner from "Components/Loading/LoadingSpinner.vue";
import AudioPlayer from "Components/Player/AudioPlayer.vue";
import { push } from "notivue";
import SongMetaData from "Views/Music/Songs/Song/SongMetaData.vue";
import SongTitle from "Views/Music/Songs/Song/SongTitle.vue";
import { ref, watch } from "vue";
import { useRoute } from "vue-router";
const store = usePlayerStore();
const route = useRoute();
const isLoading = ref(false);
const data = ref(null);
const hasError = ref(false);
const fetchData = () => {
    data.value = null;
    isLoading.value = true;
    hasError.value = false;
    axios
        .get(`/api/music/songs/${route.params.id}`)
        .then(response => {
            data.value = response.data;
        })
        .catch(error => {
            console.error(error);
            push.error({
                title: error.code,
                message: error.response.data.message || error.message
            });
            hasError.value = true;
        })
        .finally(() => {
            isLoading.value = false;
        });
};
const onEnded = async () => {
    if (data.value.nav.next && store.autoplay) {
        push.success({
            title: "Nächster Song",
            message: `${data.value.nav.next.name}`
        });
        await router.push({
            to: "song",
            params: {
                id: data.value.nav.next.encodedPath
            }
        });
    }
};
watch(() => route.params.id, fetchData, { immediate: true });
</script>

<template>
    <section class="widget">
        <div class="loading-spinner__outer" v-if="isLoading">
            <loading-spinner :size="8" />
        </div>
        <show-error v-else-if="hasError && !isLoading" @refresh="fetchData()" />
        <div v-else class="song">
            <song-title :song="data" />
            <audio-player
                :src="`/storage/${data.mp3Path}`"
                :title="data.name"
                :autoplay="store.autoplay"
                @player-ended="onEnded"
            />
            <song-meta-data :song="data" />
        </div>
    </section>
</template>

<style lang="scss" scoped>
section {
    min-height: 120px;
}

.song {
    display: flex;
    flex-direction: column;

    gap: 2ch;
}
</style>
