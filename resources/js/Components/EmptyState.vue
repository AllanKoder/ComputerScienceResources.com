<script setup>
import { Icon } from "@iconify/vue";

defineProps({
    icon: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    message: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div
        class="text-center text-gray-500 py-8 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50"
    >
        <Icon
            :icon="icon"
            class="w-12 h-12 mx-auto mb-4 text-gray-400"
        />
        <h3 class="text-lg font-semibold text-gray-700">{{ title }}</h3>
        <p class="mt-1 text-sm">{{ message }}</p>
    </div>
</template>
