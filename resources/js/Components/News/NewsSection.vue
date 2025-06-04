<script setup>
import { ref } from "vue";
import NewsItem from "@/Components/NewsItem.vue";
import NewsDialog from "@/Components/News/NewsDialog.vue";
import { Icon } from "@iconify/vue";

const props = defineProps({
    newsItems: {
        type: Array,
        required: true
    }
});

const showNewsDialog = ref(false);
</script>

<template>
    <!-- News Section - Desktop -->
    <aside class="hidden lg:block w-1/4 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="bg-secondary dark:bg-gray-700 -m-6 p-4 mb-0 flex items-center gap-2">
            <Icon icon="mdi:newspaper" class="w-6 h-6 text-primary dark:text-white" />
            <h2 class="font-bold text-primary dark:text-white">Latest News</h2>
        </div>
        <div class="space-y-4">
            <NewsItem
                v-for="(news, index) in newsItems"
                :key="index"
                :news="news"
            />
        </div>
    </aside>

    <!-- News Button - Mobile -->
    <button
        class="fixed bottom-4 right-4 lg:hidden bg-primary text-white rounded-full p-4 shadow-lg hover:bg-primaryDark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary z-50"
        @click="showNewsDialog = true"
    >
        <Icon icon="mdi:newspaper" class="w-6 h-6" />
    </button>

    <!-- News Dialog for Mobile -->
    <NewsDialog
        :show="showNewsDialog"
        :news-items="newsItems"
        @close="showNewsDialog = false"
    />
</template>
