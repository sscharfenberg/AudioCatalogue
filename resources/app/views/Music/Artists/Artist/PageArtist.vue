<script setup lang="ts">
import axios from "axios";
import ShowError from "Components/Error/ShowError.vue";
import LoadingSpinner from "Components/Loading/LoadingSpinner.vue";
import TabbedNavigation from "Components/TabbedNavigation/TabbedNavigation.vue";
import { push } from "notivue";
import { ref, watch } from "vue";
import { useRoute } from "vue-router";
import ArtistMetaData from "./ArtistMetaData.vue";
const route = useRoute();
const isLoading = ref(false);
const data = ref(null);
const hasError = ref(false);
const currentTabIndex = ref(0);
const fetchData = () => {
    data.value = null;
    isLoading.value = true;
    hasError.value = false;

    axios
        .get(`/api/music/artists/${route.params.id}`)
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
                    { idx: 0, label: 'Alben', checked: currentTabIndex === 0 },
                    { idx: 1, label: 'Songs', checked: currentTabIndex === 1 }
                ]"
                @tabchange="onTabChange"
            />
            <span v-if="currentTabIndex === 0">Tab 0</span>
            <span v-if="currentTabIndex === 1">Tab 1</span>
        </div>
    </section>
</template>
