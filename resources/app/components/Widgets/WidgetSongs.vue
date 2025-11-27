<script setup lang="ts">
import { formatSeconds } from "@/formatters/numbers";
import axios from "axios";
import AppIcon from "Components/AppIcon/AppIcon.vue";
import AppWidget from "Components/Widget/AppWidget.vue";
import { push } from "notivue";
import { onMounted, ref } from "vue";
const isLoading = ref(false);
const data = ref({});
const hasError = ref(false);
const fetchData = () => {
    isLoading.value = true;
    hasError.value = false;
    axios
        .get("/api/widget/song")
        .then(response => {
            if (response.data?.length) {
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
    <app-widget :loading="isLoading" icon="music" :error="hasError" @refresh="fetchData()" :refresh-button="true">
        <template #title>Songs</template>
        <template #body>
            <nav class="stats" v-if="data?.length">
                <router-link
                    class="stats__item stats__item--link"
                    v-for="song in data"
                    :key="song.id"
                    :to="{ name: 'song', params: { id: song.encodedPath } }"
                >
                    <span class="stats__item-meta">
                        <span>
                            <app-icon name="music" />
                            {{ song.name }}
                        </span>
                    </span>
                    <span class="stats__item-row">
                        <span class="subitem highlight">
                            <app-icon name="artist" />
                            {{ song.artist.name }}
                        </span>
                        <span class="subitem pull-right">
                            <app-icon name="time" />
                            {{ formatSeconds(song.duration) }}
                        </span>
                    </span>
                </router-link>
            </nav>
        </template>
        <template #footer>
            <router-link class="btn primary" :to="{ name: 'songs' }">
                <app-icon name="music" />
                Alle Songs
            </router-link>
        </template>
    </app-widget>
</template>
