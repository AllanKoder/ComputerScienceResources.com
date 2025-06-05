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
        <h3 class="font-semibold">
            {{ news.title }}
        </h3>
        <img
            :src="news.thumbnail"
            :alt="news.title"
            class="w-full h-32 object-cover rounded mb-2"
        />
        <p class="text-sm text-gray-600">
            {{ news.excerpt }}
        </p>
    </div>
</template>
