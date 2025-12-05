<script setup lang="ts">
import AppIcon from "Components/AppIcon/AppIcon.vue";
import { ref } from "vue";
const showBody = ref(false);
</script>

<template>
    <div class="accordion">
        <button class="accordion-head" :class="{ active: showBody }" @click="showBody = !showBody">
            <slot name="head" />
            <span class="trenner" />
            <app-icon v-show="showBody" name="accordion-close" />
            <app-icon v-show="!showBody" name="accordion-open" />
        </button>
        <div v-show="showBody" class="body"><slot name="body" /></div>
    </div>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;
@use "Abstracts/colors" as c;
@use "Abstracts/timings" as ti;

.accordion {
    display: flex;
    flex-wrap: wrap;

    &:not(:last-of-type) {
        margin-bottom: 1ch;
    }
}

.accordion-head {
    display: flex;
    align-items: center;

    flex: 0 0 100%;

    padding: 0;
    border: 0;
    margin: 0;
    gap: 1ch;

    background: transparent;
    color: map.get(c.$main, "accordion-surface");

    font-weight: 300;

    line-height: 1.4;

    cursor: pointer;

    transition: color map.get(ti.$timings, "fast") linear;

    @include m.mqset(
        "font-size",
        #{map.get(s.$main, "subheadline-font-size", "base")},
        #{map.get(s.$main, "subheadline-font-size", "portrait")},
        #{map.get(s.$main, "subheadline-font-size", "landscape")},
        #{map.get(s.$main, "subheadline-font-size", "desktop")}
    );

    .trenner {
        display: block;
        flex-grow: 1;

        background-color: map.get(c.$main, "accordion-line-background");
        border-radius: map.get(s.$main, "subheadline-line-radius");

        transition: background-color map.get(ti.$timings, "fast") linear;

        @include m.mqset(
            "height",
            #{map.get(s.$main, "subheadline-line-height", "base")},
            #{map.get(s.$main, "subheadline-line-height", "portrait")},
            #{map.get(s.$main, "subheadline-line-height", "landscape")},
            #{map.get(s.$main, "subheadline-line-height", "desktop")}
        );
    }

    .icon {
        transition: fill map.get(ti.$timings, "fast") linear;
    }

    &.active {
        color: map.get(c.$main, "accordion-open");

        .trenner {
            background-color: map.get(c.$main, "accordion-open");
        }

        .icon {
            fill: map.get(c.$main, "accordion-open");
        }
    }
}

.body {
    flex-grow: 1;
}
</style>
