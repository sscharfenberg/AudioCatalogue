<script setup lang="ts">
import { formatDecimals, formatSeconds } from "@/formatters/numbers";
import AppIcon from "Components/AppIcon/AppIcon.vue";
import AppWidget from "Components/Widget/AppWidget.vue";
import axios from "axios";
import { push } from "notivue";
import { onMounted, ref } from "vue";
const isLoading = ref(false);
const data = ref({});
const hasError = ref(false);
const fetchData = () => {
    isLoading.value = true;
    hasError.value = false;
    axios
        .get("/api/widget/artist")
        .then(response => {
            if (response.data?.length > 0) {
                hasError.value = false;
                data.value = response.data;
            }
        })
        .catch(error => {
            console.error(error);
            push.error({
                title: error.code,
                message: error.response?.data?.message || error.message
            });
            hasError.value = true;
        })
        .finally(() => {
            isLoading.value = false;
        });
};
onMounted(() => {
    fetchData();
});
</script>

<template>
    <app-widget
        :loading="isLoading"
        icon="album"
        :error="hasError"
        @refresh="fetchData()"
        :refresh-button="true"
        ajax-url="/api/music/search/artists"
    >
        <template #title>Künstler</template>
        <template #body>
            <nav class="stats" v-if="data?.length && !hasError">
                <router-link
                    v-for="artist in data"
                    :key="artist.id"
                    class="stats__item stats__item--link"
                    :to="{ name: 'artist', params: { id: artist.encodedName } }"
                >
                    <span class="stats__item-meta">
                        <span class="highlight">
                            <app-icon name="artist" />
                            {{ artist.name }}
                        </span>
                        <span class="subitem">
                            <app-icon name="album" />
                            {{ artist.albums.length }}
                        </span>
                    </span>
                    <span class="stats__item-row">
                        <span class="subitem highlight">
                            <app-icon name="time" />
                            {{ formatSeconds(artist.songsDuration) }}
                        </span>
                        <span class="subitem pull-right">
                            {{ formatDecimals(artist.numSongs) }}
                            Songs
                        </span>
                    </span>
                </router-link>
            </nav>
        </template>
        <template #footer>
            <router-link class="btn primary" :to="{ name: 'artists' }">
                <app-icon name="artist" />
                Alle Künstler
            </router-link>
        </template>
    </app-widget>
</template>
