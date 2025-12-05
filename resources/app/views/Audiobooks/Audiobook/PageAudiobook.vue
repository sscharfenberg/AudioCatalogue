<script setup lang="ts">
import axios from "axios";
import ShowError from "Components/Error/ShowError.vue";
import LoadingSpinner from "Components/Loading/LoadingSpinner.vue";
import AudioPlayer from "Components/Player/AudioPlayer.vue";
import { push } from "notivue";
import AudiobookTracks from "Views/Audiobooks/Audiobook/AudiobookTracks.vue";
import { ref, watch } from "vue";
import { useRoute } from "vue-router";
import AudiobookMetaData from "./AudiobookMetaData.vue";
import AudiobookTitle from "./AudiobookTitle.vue";
const isLoading = ref(false);
const data = ref(null);
const hasError = ref(false);
const route = useRoute();
const autoplay = ref(false);
const fetchData = () => {
    data.value = null;
    isLoading.value = true;
    hasError.value = false;
    axios
        .get(`/api/audiobooks/${route.params.id}`)
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
watch(() => route.params.id, fetchData, { immediate: true });
const onPlay = (value: string) => {
    axios
        .get(`/api/audiobooks/play/${value}`)
        .then(response => {
            console.log(response.data);
        })
        .catch(error => {
            console.error(error);
        })
        .finally(() => {
            console.log("xhr finished.");
        });
};
</script>

<template>
    <section class="widget">
        <div class="loading-spinner__outer" v-if="isLoading">
            <loading-spinner :size="8" />
        </div>
        <show-error v-else-if="hasError && !isLoading" @refresh="fetchData()" />
        <div v-if="!hasError && !isLoading && data?.tracks?.length" class="audiobook">
            <audiobook-title :title="data.name" :cover="data.cover" />
            <audio-player src="test" title="fuddel" :autoplay="autoplay" />
            <audiobook-meta-data :book="data" />
            <audiobook-tracks :tracks="data.tracks" @play="onPlay" />
        </div>
    </section>
</template>
