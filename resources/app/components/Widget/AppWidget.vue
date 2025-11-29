<script setup lang="ts">
import ShowError from "Components/Error/ShowError.vue";
import AjaxSearch from "Components/Search/AjaxSearch.vue";
import WidgetBody from "./WidgetBody.vue";
import WidgetFooter from "./WidgetFooter.vue";
import WidgetLoader from "./WidgetLoader.vue";
import WidgetTitle from "./WidgetTitle.vue";
defineProps({
    loading: {
        type: Boolean,
        default: false
    },
    icon: {
        type: String
    },
    error: {
        type: Boolean,
        default: false
    },
    refreshButton: {
        type: Boolean,
        default: false
    },
    ajaxUrl: String
});
</script>

<template>
    <div class="widget">
        <widget-title
            v-if="$slots.title && !loading"
            :icon="icon"
            :refresh-button="refreshButton"
            @refresh="$emit('refresh')"
        >
            <slot name="title" />
        </widget-title>
        <ajax-search v-if="ajaxUrl" :ajax-url="ajaxUrl" />
        <widget-body v-if="$slots.body && !loading && !error">
            <slot name="body" />
        </widget-body>
        <show-error v-if="error && !loading" @refresh="$emit('refresh')" />
        <widget-footer v-if="$slots.footer && !loading">
            <slot name="footer" />
        </widget-footer>
        <widget-loader v-if="loading" />
    </div>
</template>
