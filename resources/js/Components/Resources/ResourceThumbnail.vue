<script setup>
import { defineProps } from "vue";

defineProps({
    src: {
        type: [String, null],
        required: true,
    },
    alt: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div
        class="w-full h-full overflow-hidden rounded-md object-cover transition-transform hover:scale-105"
    >
        <img
            v-if="src"
            :src="src"
            :alt="alt"
            class="w-44 h-44 object-contain rounded-lg p-1"
        />
        <img
            v-else
            src="/images/Logo.svg"
            :alt="alt"
            class="w-44 h-44 object-contain rounded-lg p-1"
        />
    </div>
</template>
