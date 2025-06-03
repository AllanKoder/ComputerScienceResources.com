<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import NewsItem from "@/Components/NewsItem.vue";
import { Icon } from "@iconify/vue";

defineProps({
    show: {
        type: Boolean,
        required: true
    },
    newsItems: {
        type: Array,
        required: true
    }
});

defineEmits(['close']);
</script>

<template>
    <DialogModal :show="show" @close="$emit('close')" max-width="lg">
        <template #title>
            <div class="bg-secondary dark:bg-gray-700 -m-6 p-6 mb-0 flex items-center gap-2">
                <Icon icon="mdi:newspaper" class="w-6 h-6 text-primary dark:text-white" />
                <h2 class="text-xl font-bold text-primary dark:text-white">Latest News</h2>
            </div>
        </template>

        <template #content>
            <div class="divide-y divide-secondary dark:divide-gray-700 -mx-6">
                <div class="space-y-0 max-h-[60vh] overflow-y-auto">
                    <NewsItem
                        v-for="(news, index) in newsItems"
                        :key="index"
                        :news="news"
                        class="px-6 hover:bg-secondary/50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                    />
                </div>
            </div>
        </template>

        <template #footer>
            <div class="bg-secondary/30 dark:bg-gray-700/30 -m-6 p-6 mt-0 flex justify-end">
                <button
                    type="button"
                    class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-semibold text-sm tracking-wide hover:bg-primaryDark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary active:scale-95 transform transition-all duration-200"
                    @click="$emit('close')"
                >
                    Close
                </button>
            </div>
        </template>
    </DialogModal>
</template>

<style scoped>
/* Add any additional scoped styles here */
</style>
