<script setup lang="ts">
import { usePlayerStore } from "@/stores/player";
import MonoSelect from "Components/Select/MonoSelect.vue";
import { computed, onMounted } from "vue";
const store = usePlayerStore();
const props = defineProps({
    tracks: {
        type: Array,
        required: true
    },
    bookEncodedName: {
        type: String,
        required: true
    }
});
const emit = defineEmits<{
    (e: "play", value: string): void;
}>();
const trackOptions = props.tracks.map(t => {
    let discs = "";
    if (t.discs > 1) {
        discs = "Disc " + t.disc + "/" + t.discs + " - ";
    }
    return { label: `${discs} ${t.track} - ${t.name}`, value: t.encodedPath };
});
const onChange = (value: string) => {
    store.setAudiobookBookmark(props.bookEncodedName, value, 0);
    if (value) {
        emit("play", value);
    }
};
const currentTrack = computed(() => store.getAudiobookBookmark(props.bookEncodedName));
onMounted(() => {
    if (currentTrack.value?.trackEncodedPath) {
        onChange(currentTrack.value.trackEncodedPath);
    }
});
</script>

<template>
    <div class="bookmark">
        Kapitel
        <mono-select
            :options="trackOptions"
            :selected="currentTrack ? currentTrack.trackEncodedPath : null"
            placeholder="Bitte wählen"
            @change="onChange"
        />
    </div>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/mixins" as m;

.bookmark {
    display: flex;
    align-items: center;

    flex-grow: 1;

    height: 100%;
    gap: 1ch;

    background-color: map.get(c.$main, "row1-background");
    color: map.get(c.$main, "row1-surface");

    list-style: none;

    text-decoration: none;

    @include m.mqset(
        "padding",
        #{map.get(s.$main, "row-padding", "base")},
        #{map.get(s.$main, "row-padding", "portrait")},
        #{map.get(s.$main, "row-padding", "landscape")},
        #{map.get(s.$main, "row-padding", "desktop")}
    );
}
</style>
