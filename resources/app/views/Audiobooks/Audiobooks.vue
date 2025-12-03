<script setup lang="ts">
const props = defineProps({
    authors: {
        type: Array,
        required: true
    },
    books: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div>{{ books }}</div>
</template>
