<script setup>
defineProps({
  news: {
    type: Object,
    required: true
  }
});
</script>

<template>
  <div class="mb-4">
    <img
      :src="news.thumbnail"
      :alt="news.title"
      class="w-full h-32 object-cover rounded mb-2"
    />
    <h3 class="text-lg font-semibold">
      {{ news.title }}
    </h3>
    <p class="text-sm text-gray-600">
      {{ news.excerpt }}
    </p>
  </div>
</template>
