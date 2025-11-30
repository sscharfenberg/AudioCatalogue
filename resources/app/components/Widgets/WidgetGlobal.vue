<script setup lang="ts">
import { formatBytes, formatDecimals, formatSeconds } from "@/formatters/numbers";
import axios from "axios";
import AppIcon from "Components/AppIcon/AppIcon.vue";
import AppWidget from "Components/Widget/AppWidget.vue";
import { format } from "date-fns";
import { push } from "notivue";
import { onMounted, ref } from "vue";
const isLoading = ref(true);
const data = ref({});
const hasError = ref(false);
const fetchData = () => {
    isLoading.value = true;
    hasError.value = false;
    axios
        .get("/api/widget/global")
        .then(response => {
            if (response.data && response.data.music) {
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
    <app-widget :loading="isLoading" icon="dashboard" :error="hasError" @refresh="fetchData()" :refresh-button="true">
        <template #title> Globale Stats </template>
        <template #body>
            <ul class="stats">
                <li class="stats__item">
                    <div class="stats__item-meta">
                        <span class="stats__item-hdl">
                            <app-icon name="music" />
                            Musik
                        </span>
                        <span class="highlight">
                            <app-icon name="file" />
                            {{ formatBytes(data.music.size) }}
                        </span>
                    </div>
                    <div class="stats__item-row">
                        <span class="subitem">{{ formatDecimals(data.music.files) }} Dateien</span>
                        <span class="subitem pull-right">
                            <app-icon name="time" />
                            {{ formatSeconds(data.music.duration) }}
                        </span>
                    </div>
                </li>
                <li class="stats__item">
                    <div class="stats__item-meta">
                        <span class="stats__item-hdl">
                            <app-icon name="audiobook" />
                            Hörbücher
                        </span>
                        <span class="highlight">
                            <app-icon name="file" />
                            {{ formatBytes(data.audiobooks.size) }}
                        </span>
                    </div>
                    <div class="stats__item-row">
                        <span class="subitem">{{ formatDecimals(data.audiobooks.files) }} Dateien</span>
                        <span class="subitem pull-right">
                            <app-icon name="time" />
                            {{ formatSeconds(data.audiobooks.duration) }}
                        </span>
                    </div>
                </li>
                <li class="stats__item">
                    <div class="stats__item-meta">
                        <span class="stats__item-hdl">
                            <app-icon name="sum" />
                            Gesamt
                        </span>
                        <span class="highlight">
                            <app-icon name="file" />
                            {{ formatBytes(data.total.size) }}
                        </span>
                    </div>
                    <div class="stats__item-row">
                        <span class="subitem">{{ formatDecimals(data.total.files) }} Dateien</span>
                        <span class="subitem pull-right">
                            <app-icon name="time" class="pull-right" />
                            {{ formatSeconds(data.total.duration) }}
                        </span>
                    </div>
                </li>
                <li class="stats__item">
                    <app-icon name="datetime" />
                    Letzte Aktualisierung
                    <span class="highlight pull-right">{{ format(data.last_full_update, "dd.MM.yyyy HH:mm:ss") }}</span>
                </li>
            </ul>
        </template>
    </app-widget>
</template>
