<script setup lang="ts">
import axios from "axios";
import ShowError from "Components/Error/ShowError.vue";
import LoadingSpinner from "Components/Loading/LoadingSpinner.vue";
import TabbedNavigation from "Components/TabbedNavigation/TabbedNavigation.vue";
import { push } from "notivue";
import { onMounted, ref } from "vue";
import AudiobookAuthors from "./AudiobookAuthors.vue";
import Audiobooks from "./Audiobooks.vue";
const isLoading = ref(false);
const data = ref(null);
const hasError = ref(false);
const currentTabIndex = ref(0);
const fetchData = () => {
    data.value = null;
    isLoading.value = true;
    hasError.value = false;
    axios
        .get(`/api/audiobooks`)
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
onMounted(() => {
    fetchData();
});
const onTabChange = val => (currentTabIndex.value = val);
</script>

<template>
    <section class="widget">
        <div class="loading-spinner__outer" v-if="isLoading">
            <loading-spinner :size="8" />
        </div>
        <show-error v-else-if="hasError && !isLoading" @refresh="fetchData()" />
        <div v-if="!hasError && !isLoading && data?.authors" class="audiobooks">
            <tabbed-navigation
                name="tabbed-navigation-artist"
                :tabs="[
                    {
                        idx: 0,
                        label: `Audiobooks (${data?.audiobooks?.length})`,
                        icon: 'audiobooks',
                        checked: currentTabIndex === 0
                    },
                    {
                        idx: 1,
                        label: `Autoren (${data?.authors?.length})`,
                        icon: 'author',
                        checked: currentTabIndex === 1
                    }
                ]"
                @tabchange="onTabChange"
            >
                <audiobooks v-show="currentTabIndex === 0" :authors="data.authors" :books="data.audiobooks" />
                <audiobook-authors v-show="currentTabIndex === 1" :authors="data.authors" :books="data.audiobooks" />
            </tabbed-navigation>
        </div>
    </section>
</template>

<style lang="scss" scoped>
section {
    min-height: 120px;
}

.audiobooks {
    display: flex;
    flex-direction: column;

    gap: 2ch;
}
</style>
