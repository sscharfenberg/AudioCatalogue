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
        .get("/api/widget/audiobook")
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
        icon="audiobooks"
        :error="hasError"
        @refresh="fetchData()"
        :refresh-button="true"
        ajax-url="/api/audiobooks/search"
    >
        <template #title>Audiobooks</template>
        <template #body>
            <nav class="stats" v-if="data?.length">
                <router-link
                    v-for="book in data"
                    :key="book.id"
                    class="stats__item stats__item--link"
                    :to="{ name: 'audiobook', params: { id: book.encodedName } }"
                >
                    <img v-if="book.thumbnail" class="thumbnail" :src="book.thumbnail" alt="Thumbnail" />
                    <span class="stats__col">
                        <span class="stats__item-meta">
                            <span class="stats__item-hdl">
                                <app-icon name="audiobook" />
                                {{ book.name }}
                            </span>
                        </span>
                        <span class="stats__item-row">
                            <span class="subitem">
                                <app-icon name="time" />
                                {{ formatSeconds(book.duration) }}
                            </span>
                        </span>
                        <span class="stats__item-row">
                            <span class="highlight">
                                <app-icon name="author" />
                                {{ book.authors.map(b => b.name).join(",") }}
                            </span>
                        </span>
                    </span>
                </router-link>
            </nav>
        </template>
        <template #footer>
            <router-link class="btn primary" :to="{ name: 'audiobooks' }">
                <app-icon name="audiobooks" />
                Alle Audiobooks
            </router-link>
        </template>
    </app-widget>
</template>
