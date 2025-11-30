<script setup lang="ts">
import axios from "axios";
import LoadingSpinner from "Components/Loading/LoadingSpinner.vue";
import SearchResult from "Components/Search/SearchResult.vue";
import debounce from "lodash/debounce";
import { push } from "notivue";
import { onBeforeMount, onUnmounted, ref, useTemplateRef } from "vue";
const search = ref("");
const results = ref([]);
const isLoading = ref(false);
const showResults = ref(false);
const props = defineProps({
    ajaxUrl: {
        type: String,
        required: true
    }
});
const input = useTemplateRef("search-input");
const fetchData = () => {
    isLoading.value = true;
    results.value = [];
    showResults.value = false;
    axios
        .get(`${props.ajaxUrl}/${search.value}`)
        .then(response => {
            if (response.data?.results.length > 0) {
                results.value = response.data.results;
            } else {
                showResults.value = true;
            }
            search.value = response.data?.searchTerm;
            showResults.value = true;
        })
        .catch(error => {
            console.error(error);
            push.error({
                title: error.code,
                message: error.response?.data?.message || error.message
            });
            showResults.value = false;
        })
        .finally(() => {
            isLoading.value = false;
        });
};
const debouncedFetch = debounce(
    () => {
        if (search.value.length > 0) {
            fetchData();
        } else {
            results.value = [];
        }
    },
    500,
    { maxWait: 5000 }
);
const onClickOutSide = ev => {
    if (!(input.value === ev.target || input.value.contains(ev.target))) {
        showResults.value = false;
    }
};
const onFocus = () => {
    if (results.value.length > 0) {
        showResults.value = true;
    }
};
onBeforeMount(() => {
    document.addEventListener("click", onClickOutSide);
});
onUnmounted(() => {
    document.removeEventListener("click", onClickOutSide);
});
</script>

<template>
    <div class="search-input">
        <input
            type="text"
            class="form-input"
            placeholder="Search..."
            v-model="search"
            @input="
                showResults = false;
                debouncedFetch();
            "
            :readonly="isLoading ? true : null"
            ref="search-input"
            @focus="onFocus"
        />
        <loading-spinner v-if="isLoading" :size="2" />
        <search-result v-if="!isLoading && showResults && search.length > 0" :term="search" :results="results" />
    </div>
</template>

<style lang="scss" scoped>
.search-input {
    position: relative;
}

.loading-spinner {
    position: absolute;
    top: 6px;
    right: 7px;
}
</style>
