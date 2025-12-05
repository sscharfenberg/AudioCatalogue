<script setup lang="ts">
import { formatBytes, formatSeconds } from "@/formatters/numbers";
import AppIcon from "Components/AppIcon/AppIcon.vue";

const props = defineProps({
    book: {
        type: Object,
        required: true
    }
});
const authors = props.book.authors?.map(author => author.name).join(", ");
const narrators = props.book.narrators?.map(narrator => narrator.name).join(", ");
</script>

<template>
    <ul class="details-metadata">
        <li
            v-if="book.authors.length"
            class="highlight"
            :class="{ full: book.authors.length > 1 }"
            v-tippy="{ content: book.authors.length === 1 ? 'Autor' : 'Autoren' }"
        >
            <app-icon name="author" />
            {{ authors }}
        </li>
        <li v-if="book.narrators.length" :class="{ full: book.narrators.length > 1 }" v-tippy="{ content: 'Erzähler' }">
            <app-icon name="narrator" />
            {{ narrators }}
        </li>
        <li v-if="book.duration" v-tippy="{ content: 'Laufzeit' }">
            <app-icon name="time" />
            {{ formatSeconds(book.duration) }}
        </li>
        <li v-if="book.duration" v-tippy="{ content: 'Dateigröße' }">
            <app-icon name="file" />
            {{ formatBytes(book.size) }}
        </li>
        <li v-if="book.year" v-tippy="{ content: 'Jahr' }">
            <app-icon name="datetime" />
            {{ book.year }}
        </li>
        <li v-if="book.numTracks" v-tippy="{ content: 'Kapitel' }">{{ book.numTracks }} Kapitel</li>
    </ul>
</template>
