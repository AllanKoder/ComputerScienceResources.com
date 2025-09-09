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
        required: false,
        default: "",
    },
});
</script>

<template>
    <div
        class="text-center text-gray-500 dark:text-gray-400 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900/60"
    >
        <Icon
            :icon="icon"
            class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-500"
        />
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ title }}</h3>
        <p class="mt-1 text-sm dark:text-gray-400">{{ message }}</p>
    </div>
</template>
