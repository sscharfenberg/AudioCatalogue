<script setup lang="ts">
import axios from "axios";
import AppButton from "Components/Form/Button/AppButton.vue";
import MonoSelect from "Components/Form/Select/MonoSelect.vue";
import { push } from "notivue";
import { ref } from "vue";
import { useRoute } from "vue-router";
const route = useRoute();
const isLoading = ref(false);
const props = defineProps({
    playlists: {
        type: Array<PlaylistOptions>,
        required: true
    }
});
const mapPlaylists = lists => {
    return lists.map(list => {
        return {
            value: list.id,
            label: list.name
        };
    });
};
const availablePlaylists = ref(mapPlaylists(props.playlists));
const playlistId = ref(availablePlaylists.value.length > 0 ? availablePlaylists.value[0].value : "");
interface PlaylistOptions {
    id: string;
    name: string;
}
const onChangeSelection = val => (playlistId.value = val);
const onAdd = () => {
    isLoading.value = true;
    axios
        .post(`/api/playlists/add-song`, { playlistId: playlistId.value, songPath: route.params.id })
        .then(response => {
            push.success({
                title: "Song hinzugefügt ✅",
                message: `Song '${response.data.newEntry.song}' zu Playlist '${props.playlists.find(playlist => playlist.id === playlistId.value).name}' hinzugefügt.`
            });
            availablePlaylists.value = mapPlaylists(response.data.playlists);
            playlistId.value = availablePlaylists.value.length > 0 ? availablePlaylists.value[0].value : "";
        })
        .catch(error => {
            console.error(error);
            push.error({
                title: error.code,
                message: error.response.data.message || error.message
            });
        })
        .finally(() => {
            isLoading.value = false;
        });
};
</script>

<template>
    <div class="playlist__add">
        <mono-select :options="availablePlaylists" :selected="playlistId" @change="onChangeSelection" />
        <app-button icon="playlist_add" text="Zu Playlist hinzufügen" @click="onAdd" />
    </div>
</template>
