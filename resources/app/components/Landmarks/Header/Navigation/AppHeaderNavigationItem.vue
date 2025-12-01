<script setup lang="ts">
import AppIcon from "Components/AppIcon/AppIcon.vue";
defineProps({
    name: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    icon: {
        type: String,
        required: true
    }
});
</script>

<template>
    <router-link :to="{ name: name }" :disabled="['playlists'].includes(name) ? 'disabled' : null">
        <app-icon :name="icon" />
        <span>{{ label }}</span>
    </router-link>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

a {
    display: flex;
    align-items: center;

    padding: map.get(s.$header, "navitem-padding", "base");
    border: map.get(s.$header, "navitem-border") solid map.get(c.$header, "navitem-border");

    gap: 4px;

    background-color: map.get(c.$header, "navitem-background");
    color: map.get(c.$header, "navitem-surface");
    border-radius: map.get(s.$header, "navitem-radius");

    text-decoration: none;

    @include m.mq("portrait") {
        padding: map.get(s.$header, "navitem-padding", "base") map.get(s.$header, "navitem-padding", "portrait");
    }

    &:hover {
        background-color: map.get(c.$header, "navitem-background-hover");
        color: map.get(c.$header, "navitem-surface-hover");
        border-color: map.get(c.$header, "navitem-border-hover");
    }

    &.router-link-active {
        background-color: map.get(c.$header, "navitem-background-selected");
        color: map.get(c.$header, "navitem-surface-selected");
        border-color: map.get(c.$header, "navitem-surface-selected");
    }

    span {
        display: none;

        @include m.mq("portrait") {
            display: block;
            flex-grow: 1;
        }
    }

    &[disabled] {
        opacity: 0.5;

        cursor: not-allowed;
        pointer-events: none;
    }
}
</style>
