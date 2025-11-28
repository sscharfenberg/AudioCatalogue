<script setup lang="ts">
import AppIcon from "Components/AppIcon/AppIcon.vue";
import AutoplaySwitch from "./AutoplaySwitch.vue";
defineProps({
    nav: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <div class="song-buttons">
        <router-link
            :disabled="nav.prev?.encodedPath ? null : true"
            :to="{ name: 'song', params: { id: nav.prev?.encodedPath } }"
            v-tippy="{ content: `${nav.prev?.track} ${nav.prev?.name}` }"
            class="btn default short"
        >
            <app-icon name="prev" />
        </router-link>
        <router-link
            :disabled="nav.next?.encodedPath ? null : true"
            :to="{ name: 'song', params: { id: nav.next?.encodedPath } }"
            v-tippy="{ content: `${nav.next?.track} ${nav.next?.name}` }"
            class="btn default short"
        >
            <app-icon name="next" />
        </router-link>
        <autoplay-switch />
    </div>
</template>

<style scoped lang="scss">
.song-buttons {
    display: flex;
    align-items: center;

    gap: 1ch;
}
</style>
