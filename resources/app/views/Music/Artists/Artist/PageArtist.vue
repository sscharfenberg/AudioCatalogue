<script setup lang="ts">
import axios from "axios";
import ShowError from "Components/Error/ShowError.vue";
import LoadingSpinner from "Components/Loading/LoadingSpinner.vue";
import TabbedNavigation from "Components/TabbedNavigation/TabbedNavigation.vue";
import { push } from "notivue";
import ArtistAlbumsTable from "Views/Music/Artists/Artist/ArtistAlbumsTable.vue";
import ArtistSongsTable from "Views/Music/Artists/Artist/ArtistSongsTable.vue";
import { ref, watch } from "vue";
import { useRoute } from "vue-router";
import ArtistMetaData from "./ArtistMetaData.vue";
const route = useRoute();
const isLoading = ref(false);
const data = ref(null);
const songs = ref([]);
const albums = ref([]);
const hasError = ref(false);
const currentTabIndex = ref(0);
const fetchData = () => {
    data.value = null;
    songs.value = [];
    albums.value = [];
    hasError.value = false;
    isLoading.value = true;
    axios
        .get(`/api/music/artists/${route.params.id}`)
        .then(response => {
            data.value = response.data;
            songs.value = response.data.songs;
            albums.value = response.data.albums;
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
const onTabChange = val => (currentTabIndex.value = val);
</script>

<template>
    <section class="widget">
        <div class="loading-spinner__outer" v-if="isLoading">
            <loading-spinner :size="8" />
        </div>
        <show-error v-else-if="hasError && !isLoading" @refresh="fetchData()" />
        <div v-else class="album">
            <header class="details-title">
                <div class="details-title__title">
                    <h3>{{ data.name }}</h3>
                </div>
            </header>
            <artist-meta-data :data="data" />
            <tabbed-navigation
                name="tabbed-navigation-artist"
                :tabs="[
                    { idx: 0, label: `Alben (${data.albums.length})`, icon: 'album', checked: currentTabIndex === 0 },
                    { idx: 1, label: `Songs (${data.songs.length})`, icon: 'music', checked: currentTabIndex === 1 }
                ]"
                @tabchange="onTabChange"
            >
                <artist-albums-table v-show="currentTabIndex === 0" :albums="data.albums" />
                <artist-songs-table v-show="currentTabIndex === 1" :songs="data.songs" />
            </tabbed-navigation>
        </div>
    </section>
</template>
