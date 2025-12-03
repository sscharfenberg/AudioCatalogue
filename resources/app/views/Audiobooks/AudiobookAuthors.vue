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
const getAuthorBooks = authorId => {
    return props.books.filter(book => book.authors.find(author => author.id === authorId));
};
</script>

<template>
    <ul>
        <li v-for="author in authors" :key="author.id">
            <h4>{{ author.name }}</h4>
            <p v-for="book in getAuthorBooks(author.id)">{{ book.name }}</p>
        </li>
    </ul>
</template>
