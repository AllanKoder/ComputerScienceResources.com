<script setup>
import { defineProps } from "vue";
import { Icon } from "@iconify/vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    resource: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <div class="flex flex-col items-center">
        <button @click="upvote" class="mb-2 text-gray-500 hover:text-blue-500">
            <Icon icon="mdi:chevron-up" width="24" height="24" />
        </button>
        <span class="text-lg font-bold">{{ resource.votes || 0 }}</span>
        <button @click="downvote" class="mt-2 text-gray-500 hover:text-red-500">
            <Icon icon="mdi:chevron-down" width="24" height="24" />
        </button>
    </div>
</template>
