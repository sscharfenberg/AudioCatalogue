<script setup lang="ts">
import { formatSeconds } from "@/formatters/numbers";
import axios from "axios";
import ShowError from "Components/Error/ShowError.vue";
import LoadingSpinner from "Components/Loading/LoadingSpinner.vue";
import DataTablesCore from "datatables.net-dt";
import "datatables.net-responsive-dt";
import DataTable from "datatables.net-vue3";
import { push } from "notivue";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
DataTable.use(DataTablesCore);
const router = useRouter();
const isLoading = ref(false);
const data = ref(null);
const hasError = ref(false);
const fetchData = () => {
    data.value = null;
    isLoading.value = true;
    hasError.value = false;
    axios
        .get(`/api/music/songs`)
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
const dataTableOptions = {
    columns: [
        { data: "name", title: "Name" },
        { data: "artist.name", title: "Artist", orderData: [1, 3, 2, 5, 4] },
        { data: "album.name", title: "Album" },
        { data: "album.year", title: "Jahr", type: "num" },
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
        {
            data: "disc",
            title: "CD",
            render: (data, type, row) => {
                if (type === "display" || type === "filter") {
                    if (row.disc && row.album.discs) {
                        return row.disc + "/" + row.album.discs;
                    }
                    return "";
                }
                return data;
            }
        },
        {
            data: "duration",
            title: "Dauer",
            render: (data, type) => {
                if (type === "display" || type === "filter") {
                    return formatSeconds(data);
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
        <div v-else class="songs">
            <DataTable :data="data" class="display responsive" :options="dataTableOptions" />
        </div>
    </section>
</template>

<style lang="scss" scoped>
section {
    min-height: 120px;
}

.songs {
    display: flex;
    flex-direction: column;

    gap: 2ch;
}
</style>
