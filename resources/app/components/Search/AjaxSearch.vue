<script setup lang="ts">
import axios from "axios";
import LoadingSpinner from "Components/Loading/LoadingSpinner.vue";
import debounce from "lodash/debounce";
import { push } from "notivue";
import { ref } from "vue";
const search = ref("");
const data = ref(null);
const isLoading = ref(false);
const props = defineProps({
    ajaxUrl: {
        type: String,
        required: true
    }
});
const fetchData = () => {
    isLoading.value = true;
    axios
        .get(`${props.ajaxUrl}/${search.value}`)
        .then(response => {
            if (response.data?.genres.length > 0) {
                data.value = response.data;
            } else {
                // no hits
            }
            search.value = response.data?.searchTerm;
        })
        .catch(error => {
            console.error(error);
            push.error({
                title: error.code,
                message: error.response?.data?.message || error.message
            });
        })
        .finally(() => {
            isLoading.value = false;
        });
};
const debouncedFetch = debounce(
    () => {
        fetchData();
    },
    500,
    { maxWait: 5000 }
);
</script>

<template>
    <div class="search-input">
        <input
            type="text"
            class="form-input"
            placeholder="Search..."
            v-model="search"
            @input="debouncedFetch"
            :readonly="isLoading ? true : null"
        />
        <loading-spinner v-if="isLoading" :size="2" />
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
