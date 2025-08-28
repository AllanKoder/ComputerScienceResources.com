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
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4">
        <div class="overflow-x-auto">
            <div class="w-full flex flex-col gap-2">
                <template v-for="(resource, idx) in resources.data" :key="resource.id">
                    <ResourceItem :resource="resource" />
                    <div v-if="idx < resources.data.length - 1" class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                </template>
            </div>

            <!-- Pagination Links -->
             <div class="mb-4">
                <PaginateLinks
                    :links="resources.links"
                    :from="resources.from"
                    :to="resources.to"
                    :total="resources.total"
                ></PaginateLinks>
            </div>
        </div>
    </div>
</template>
