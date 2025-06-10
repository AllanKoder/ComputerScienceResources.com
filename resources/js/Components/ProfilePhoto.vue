<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    src: String,
    alt: String,
});
</script>

<template>
    <img
        class="size-10 rounded-full object-cover"
        :src="props.src"
        :alt="props.alt"
    />
</template>
