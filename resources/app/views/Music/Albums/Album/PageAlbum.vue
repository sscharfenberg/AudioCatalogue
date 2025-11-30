<script setup lang="ts">
import { formatBytes, formatSeconds } from "@/formatters/numbers";
import axios from "axios";
import ShowError from "Components/Error/ShowError.vue";
import LoadingSpinner from "Components/Loading/LoadingSpinner.vue";
import DataTablesCore from "datatables.net-dt";
import "datatables.net-responsive-dt";
import DataTable from "datatables.net-vue3";
import { push } from "notivue";
import { ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import AlbumMetaData from "./AlbumMetaData.vue";
import AlbumTitle from "./AlbumTitle.vue";
DataTable.use(DataTablesCore);
const route = useRoute();
const router = useRouter();
const isLoading = ref(false);
const data = ref(null);
const hasError = ref(false);
const fetchData = () => {
    data.value = null;
    isLoading.value = true;
    hasError.value = false;
    axios
        .get(`/api/music/albums/${route.params.id}`)
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
const dataTableOptions = {
    columns: [
        {
            data: "disc",
            title: "CD",
            orderData: [0, 1],
            render: (data, type, row) => {
                if (row.disc && row.album.discs) {
                    return row.disc + "/" + row.album.discs;
                }
                return "-";
            }
        },
        {
            data: "track",
            title: "Track",
            render: (data, type, row) => {
                if (type === "display" || type === "filter") {
                    if (row.track <= row.album.discTracks) {
                        return row.track + "/" + row.album.discTracks;
                    }
                    return row.track;
                }
                return data;
            }
        },
        { data: "name", title: "Name" },
        { data: "artist.name", title: "Artist" },
        {
            data: "duration",
            title: "Dauer",
            render: (data, type) => {
                if (type === "display" || type === "filter") {
                    return formatSeconds(data);
                }
                return data; // for all other types
            }
        },
        {
            data: "size",
            title: "Dateigröße",
            render: (data, type) => {
                if (type === "display" || type === "filter") {
                    return formatBytes(data);
                }
                return data; // for all other types
            }
        }
    ],
    fnRowCallback: (nRow, aData) => {
        nRow.addEventListener("click", () => {
            router.push({
                name: "song",
                params: { id: aData.encodedPath }
            });
        });
    },
    order: [[1, "asc"]],
    responsive: true,
    pageLength: 25
};
</script>

<template>
    <section class="widget">
        <div class="loading-spinner__outer" v-if="isLoading">
            <loading-spinner :size="8" />
        </div>
        <show-error v-else-if="hasError && !isLoading" @refresh="fetchData()" />
        <div v-else class="album">
            <album-title :title="data.name" :cover="data.coverPath" />
            <album-meta-data :data="data" />
            <DataTable :data="data.songs" class="display responsive" :options="dataTableOptions" />
        </div>
    </section>
</template>

<style lang="scss" scoped>
section {
    min-height: 120px;
}

.album {
    display: flex;
    flex-direction: column;

    gap: 2ch;
}
</style>
