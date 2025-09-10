<script setup>
import { Icon } from "@iconify/vue";

const props = defineProps({
    isSaved: {
        type: Boolean,
        required: true,
    },
    hasContent: {
        type: Boolean,
        required: true,
    },
});
</script>

<template>
    <div
        v-if="isSaved && hasContent"
        class="absolute top-4 right-4 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 px-3 py-1 rounded-full text-sm font-medium flex items-center gap-1"
    >
        <Icon icon="mdi:content-save-outline" />
        <span>Saved locally</span>
    </div>
</template>
