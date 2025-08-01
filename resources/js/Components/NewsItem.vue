<script setup>
defineProps({
    news: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="my-4">
        <h3 class="font-semibold text-lg">
            {{ news.title }}
        </h3>

        <img
            v-if="news.thumbnail"
            :src="news.thumbnail"
            :alt="news.title"
            class="w-full h-48 object-cover rounded mb-2"
        />

        <p class="text-sm text-gray-600">
            {{ news.excerpt }}
        </p>
    </div>
</template>
