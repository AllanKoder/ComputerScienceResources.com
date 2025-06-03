<script setup>
import ResourceItem from "./ResourceItem.vue";
import PaginateLinks from "@/Components/Pagination/PaginateLinks.vue";

const props = defineProps({
    resources: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
        <table class="w-full border-separate" style="border-spacing: 0 2rem">
            <ResourceItem
                v-for="resource in resources.data"
                :key="resource.id"
                :resource="resource"
            />
        </table>

        <!-- Pagination Links -->
        <PaginateLinks :links="resources.links"></PaginateLinks>
    </div>
</template>
