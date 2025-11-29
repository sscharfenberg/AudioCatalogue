<script setup lang="ts">
import { formatBytes, formatDecimals, formatSeconds } from "@/formatters/numbers";
import axios from "axios";
import AppIcon from "Components/AppIcon/AppIcon.vue";
import AppWidget from "Components/Widget/AppWidget.vue";
import { push } from "notivue";
import { onMounted, ref } from "vue";
const isLoading = ref(false);
const data = ref(null);
const hasError = ref(false);
const fetchData = () => {
    isLoading.value = true;
    hasError.value = false;
    axios
        .get("/api/widget/genre")
        .then(response => {
            if (response.data?.length > 1) {
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
        icon="genre"
        :error="hasError"
        @refresh="fetchData()"
        :refresh-button="true"
        ajax-url="/api/music/search/genres"
    >
        <template #title>Top Genres</template>
        <template #body>
            <nav class="stats" v-if="data?.length">
                <router-link
                    v-for="genre in data"
                    :key="genre.id"
                    class="stats__item stats__item--link"
                    :to="{ name: 'genre', params: { id: genre.encodedName } }"
                >
                    <span class="stats__item-meta">
                        <span class="stats__item-hdl">{{ genre.name }}</span>
                        <span class="highlight">
                            <app-icon name="file" />
                            {{ formatBytes(genre.size) }}
                        </span>
                    </span>
                    <span class="stats__item-row">
                        <span class="subitem">
                            <app-icon name="time" />
                            {{ formatSeconds(genre.duration) }}
                        </span>
                        <span class="subitem pull-right">
                            {{ formatDecimals(genre.numSongs) }}
                            Dateien
                        </span>
                    </span>
                </router-link>
            </nav>
        </template>
        <template #footer>
            <router-link class="btn primary" :to="{ name: 'genres' }">
                <app-icon name="genre" />
                Alle Genres
            </router-link>
        </template>
    </app-widget>
</template>
