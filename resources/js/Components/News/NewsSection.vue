<script setup>
import { ref } from "vue";
import TrendingResourceItem from "@/Components/TrendingResourceItem.vue";
import NewsDialog from "@/Components/News/NewsDialog.vue";
import { Icon } from "@iconify/vue";
import EmptyState from "../EmptyState.vue";

const props = defineProps({
    hotResources: {
        type: Array,
        required: true,
    },
});

const showNewsDialog = ref(false);
</script>

<template>
    <!-- Trending Resources Section - Desktop -->
    <aside
        class="hidden h-fit lg:block w-1/4 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6"
    >
        <div
            class="bg-secondary dark:bg-gray-700 -m-6 p-4 mb-0 flex items-center gap-2"
        >
            <Icon
                icon="mdi:trending-up"
                class="w-6 h-6 text-primary dark:text-white"
            />
            <h2 class="font-bold text-primary dark:text-white">Trending Resources</h2>
        </div>
        <div class="space-y-4" v-if="hotResources.length > 0">
            <TrendingResourceItem
                v-for="(resource, index) in hotResources"
                :key="index"
                :resource="resource"
            />
        </div>
        <EmptyState class="mt-6" v-else icon="mdi:trending-up" title="No Trending Resources" />
    </aside>

    <!-- Trending Resources Button - Mobile -->
    <button
        class="fixed bottom-4 right-4 lg:hidden bg-primary text-white rounded-full p-4 shadow-lg hover:bg-primaryDark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary z-50"
        @click="showNewsDialog = true"
    >
        <Icon icon="mdi:trending-up" class="w-6 h-6" />
    </button>

    <!-- Trending Resources Dialog for Mobile -->
    <NewsDialog
        :show="showNewsDialog"
        :resource-items="hotResources"
        @close="showNewsDialog = false"
    />
</template>
