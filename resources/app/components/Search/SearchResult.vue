<script setup lang="ts">
import AppIcon from "Components/AppIcon/AppIcon.vue";

defineProps({
    term: {
        type: String,
        required: true
    },
    results: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <nav v-if="results.length > 0" class="search-result">
        <router-link
            v-for="(item, index) in results"
            :key="index"
            :to="{ name: item.link.to, params: { id: item.link.param } }"
        >
            <span class="hdl">
                <app-icon :name="item.link.to" />
                {{ item.label }}
            </span>
            <span class="aside">
                <app-icon :name="item.icon" />
                {{ item.aside }}
            </span>
        </router-link>
    </nav>
    <div v-else class="search-result">
        Keine Suchergebnisse für <strong>{{ term }}</strong
        >.
    </div>
</template>
