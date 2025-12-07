<script setup lang="ts">
import AppIcon from "Components/AppIcon/AppIcon.vue";
import { useRoute } from "vue-router";
const route = useRoute();
</script>

<template>
    <section class="page-header">
        <h2>
            <app-icon :name="route.meta.icon" />
            {{ route.meta.title }}
            <span id="specificTitle" />
        </h2>
    </section>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/typography" as t;
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;
@use "Abstracts/colors" as c;

.page-header h2 {
    display: flex;
    align-items: center;

    margin: 0 0 1ch;
    gap: 0.5ch;

    font-family: map.get(t.$main, "page-header");

    @include m.mqset(
        "font-size",
        #{map.get(s.$main, "page-header-font-size", "base")},
        #{map.get(s.$main, "page-header-font-size", "portrait")},
        #{map.get(s.$main, "page-header-font-size", "landscape")},
        #{map.get(s.$main, "page-header-font-size", "desktop")}
    );

    .icon {
        fill: map.get(c.$main, "page-header-line");
    }

    &::after {
        display: block;
        flex-grow: 1;

        background-color: map.get(c.$main, "page-header-line");
        border-top-right-radius: map.get(s.$main, "page-header-radius");
        border-bottom-right-radius: map.get(s.$main, "page-header-radius");

        content: "";

        @include m.mqset(
            "height",
            #{map.get(s.$main, "page-header-line", "base")},
            #{map.get(s.$main, "page-header-line", "portrait")},
            #{map.get(s.$main, "page-header-line", "landscape")},
            #{map.get(s.$main, "page-header-line", "desktop")}
        );
    }
}
</style>
