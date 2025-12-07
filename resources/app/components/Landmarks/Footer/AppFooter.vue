<script setup lang="ts">
import ThemeSwitch from "./ThemeSwitch.vue";
</script>

<template>
    <footer>
        <theme-switch />
        <address>Handcrafted with ❤ by Sven Scharfenberg</address>
        <a href="https://github.com/sscharfenberg/AudioCatalogue"><img src="./github.svg" alt="Github Repository" /></a>
    </footer>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;

footer {
    display: flex;
    flex-direction: column;

    gap: 1ch;

    @include m.mq("portrait") {
        align-items: center;
        flex-direction: row;

        address {
            margin-left: auto;
        }
    }

    a {
        display: flex;
        align-items: center;

        img {
            width: 24px;
            height: auto;
        }
    }
}
</style>
