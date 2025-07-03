<script setup>
import { defineProps } from 'vue';

defineProps({
    src: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        required: true,
    }
})
</script>

<template>
        <img
        :src="src"
        :alt="alt"
        class="w-full h-auto object-cover rounded-lg self-center p-4"
    />
</template>
