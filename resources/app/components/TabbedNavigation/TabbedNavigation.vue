<script setup lang="ts">
defineProps({
    name: {
        type: String,
        required: true
    },
    tabs: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <ul role="list" aria-label="Verfügbare Optionen">
        <li v-for="tab in tabs" :key="tab.idx">
            <input
                type="radio"
                :name="name"
                :id="`tab-${name}-${tab.idx}`"
                :value="tab.idx"
                :checked="tab.checked"
                @change="$emit('tabchange', tab.idx)"
            />
            <label :for="`tab-${name}-${tab.idx}`">{{ tab.label }}</label>
        </li>
    </ul>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;

ul {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(160px, 100%), 1fr));

    padding: 0;
    margin: 2ch 0 1ch;
    gap: 1ch;

    list-style: none;

    label {
        display: block;

        border: map.get(s.$main, "tabnav-border") solid transparent;

        background-color: map.get(c.$main, "tabnav-background");
        color: map.get(c.$main, "tabnav-surface");

        cursor: pointer;

        transition:
            background-color map.get(ti.$timings, "fast") linear,
            border-color map.get(ti.$timings, "fast") linear,
            color map.get(ti.$timings, "fast") linear;

        @include m.mqset(
            "padding",
            #{map.get(s.$main, "row-padding", "base")},
            #{map.get(s.$main, "row-padding", "portrait")},
            #{map.get(s.$main, "row-padding", "landscape")},
            #{map.get(s.$main, "row-padding", "desktop")}
        );

        &:hover {
            background-color: map.get(c.$main, "tabnav-background-hover");
            color: map.get(c.$main, "tabnav-surface-hover");
        }
    }

    li:first-child label {
        border-top-left-radius: map.get(s.$main, "tabnav-radius");
    }

    li:last-child label {
        border-top-right-radius: map.get(s.$main, "tabnav-radius");
    }

    input[type="radio"] {
        display: none;

        &:checked + label {
            color: map.get(c.$main, "tabnav-surface-active");
            border-color: map.get(c.$main, "tabnav-border-active");
        }
    }
}
</style>
