<script setup lang="ts">
import { formatBytes, formatSeconds } from "@/formatters/numbers";
import AppIcon from "Components/AppIcon/AppIcon.vue";

defineProps({
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
    <ul class="bookshelf">
        <li v-for="book in books" :key="book.id">
            <router-link
                :to="{ name: 'audiobook', params: { id: book.encodedName } }"
                class="bookshelf__book"
                v-tippy="{ content: `${book.name}` }"
            >
                <img v-if="book.cover" :src="book.cover" :alt="book.name" />
                <span class="bookshelf__book-meta">
                    <span class="row">
                        <span class="col">
                            <app-icon name="datetime" />
                            {{ book.year }}
                        </span>
                        <span class="col">
                            <app-icon name="time" />
                            {{ formatSeconds(book.duration) }}
                        </span>
                    </span>
                </span>
                <span class="bookshelf__book-data">
                    <span class="row">
                        <span class="col"> {{ book.numTracks }} Kapitel </span>
                        <span class="col">
                            <app-icon name="file" />
                            {{ formatBytes(book.size) }}
                        </span>
                    </span>
                </span>
            </router-link>
        </li>
    </ul>
</template>
