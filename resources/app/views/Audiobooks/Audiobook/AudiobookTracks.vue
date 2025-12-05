<script setup lang="ts">
import { formatBytes, formatSeconds } from "@/formatters/numbers";
import DataTablesCore from "datatables.net-dt";
import "datatables.net-responsive-dt";
import DataTable from "datatables.net-vue3";
DataTable.use(DataTablesCore);
const props = defineProps({
    tracks: {
        type: Array,
        required: true
    }
});
const emit = defineEmits<{
    (e: "play", value: string): void;
}>();
const dataTableOptions = {
    columns: [
        { data: "name", title: "Name" },
        {
            data: "track",
            title: "Track",
            render: (data, type, row) => {
                if (type === "display" || type === "filter") {
                    if (row.track <= props.tracks.length) {
                        return row.track + "/" + props.tracks.length;
                    }
                    return row.track;
                }
                return data;
            }
        },
        {
            data: "size",
            title: "Größe",
            render: (data, type, row) => {
                if (type === "display" || type === "filter") {
                    if (row.size) {
                        return formatBytes(row.size);
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
            emit("play", aData.path);
        });
    },
    order: [[1, "asc"]],
    responsive: true,
    pageLength: 25
};
</script>

<template>
    <DataTable :data="tracks" class="display responsive" :options="dataTableOptions" />
</template>
