<script setup lang="ts">
defineProps({
    title: {
        type: String,
        required: true
    },
    cover: String
});
</script>

<template>
    <header class="details-title">
        <div class="details-title__title">
            <h3>{{ title }}</h3>
        </div>
        <img v-if="cover && cover.length > 48" :src="cover" :alt="title" />
        <img v-else src="./missing-cover.jpg" alt="Cover fehlt!" v-tippy="{ content: 'Cover fehlt!' }" />
    </header>
</template>
