<script setup lang="ts">
import { formatBytes, formatSeconds } from "@/formatters/numbers";
import AccordionItem from "Components/Accordion/AccordionItem.vue";
import AppIcon from "Components/AppIcon/AppIcon.vue";

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
const getAuthorBooks = authorId => {
    return props.books.filter(book => book.authors.find(author => author.id === authorId));
};
</script>

<template>
    <div class="authors">
        <accordion-item v-for="author in authors" :key="author.id">
            <template #head>{{ author.name }}: {{ getAuthorBooks(author.id).length }}</template>
            <template #body>
                <ul class="bookshelf">
                    <li v-for="book in getAuthorBooks(author.id)" :key="book.id">
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
        </accordion-item>
    </div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;

.bookshelf {
    display: flex;
    flex-wrap: wrap;

    > li {
        flex: 0 0 100%;

        @include m.mq("landscape") {
            flex: 0 0 calc(50% - 1ch);
        }

        @include m.mq("desktop") {
            flex: 0 0 calc(33.33% - 1.333ch);
        }

        @include m.mq("full") {
            flex: 0 0 calc(25% - 1.5ch);
        }
    }
}
</style>
