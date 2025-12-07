<script setup lang="ts">
import AppIcon from "Components/AppIcon/AppIcon.vue";
import { nextTick, onMounted, onUnmounted, ref, useTemplateRef } from "vue";
const props = defineProps({
    options: {
        type: Array,
        required: true
    },
    selected: {
        type: String
    },
    placeholder: {
        type: String,
        default: "Bitte wählen"
    }
});
const emit = defineEmits(["change"]);
const menuOpen = ref(false);
const selectedValue = ref(props.selected);
const dropdown = useTemplateRef("dropdown");
/**
 * @function select option handler, is called when a option is selected
 * @param value
 */
const select = (value: string) => {
    if (value !== selectedValue.value) {
        selectedValue.value = value;
        emit("change", value);
    }
    menuOpen.value = false;
};
/**
 * @function toggle select menu open|closed
 */
const toggleMenu = async () => {
    menuOpen.value = !menuOpen.value; // invert value
    await nextTick(); // give vue time to update everything, so our selector actually exists
    const _option = document.querySelector('button[data-value="' + selectedValue.value + '"]');
    if (selectedValue.value && _option) {
        _option.scrollIntoView(); // https://developer.mozilla.org/en-US/docs/Web/API/Element/scrollIntoView
    }
};
/**
 * ensure a click outside the select closes the menu
 * @param ev
 */
const onClickOutSide = ev => {
    if (!(dropdown.value === ev.target || dropdown.value.contains(ev.target))) {
        menuOpen.value = false;
    }
};
/**
 * lifecycle hooks
 */
onMounted(() => {
    document.addEventListener("click", onClickOutSide);
});
onUnmounted(() => {
    document.removeEventListener("click", onClickOutSide);
});
</script>

<template>
    <div class="form-select" ref="dropdown">
        <button :class="{ open: menuOpen }" @click="toggleMenu">
            <span v-if="selectedValue">{{ options.find(option => option.value === selectedValue).label }}</span>
            <span v-else>{{ placeholder }}</span>
            <span class="actions">
                <button class="clear-btn" v-if="selectedValue" @click="select('')"><app-icon name="clear" /></button>
                <span class="caret" />
            </span>
        </button>
        <div v-if="menuOpen" class="menu">
            <div class="menu-items">
                <button
                    v-for="option in options"
                    :key="option.value"
                    :data-value="option.value"
                    :class="{
                        selected: selectedValue === option.value
                    }"
                    class="form-option"
                    @click="select(option.value)"
                >
                    {{ option.label }}
                </button>
            </div>
        </div>
    </div>
</template>
