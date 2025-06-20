<script setup>
import { defineProps } from "vue";
import ResourceReview from "./ResourceReview.vue";
import PaginateLinks from "@/Components/Pagination/PaginateLinks.vue";
import EmptyState from "@/Components/EmptyState.vue";

const props = defineProps({
    reviews: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="px-6 max-w-7xl mx-auto">
        <!-- Review List -->
        <div v-if="reviews.data.length > 0" class="space-y-6">
            <div
                v-for="review in reviews.data"
                :key="review.id"
                class="bg-white/70 backdrop-blur-md p-6 rounded-lg shadow-md"
            >
                <ResourceReview :review="review" />
            </div>
        </div>
        <EmptyState
            v-else
            icon="mdi:comment-quote-outline"
            title="No Reviews Yet"
            message="Be the first to share your thoughts on this resource!"
        />
        <PaginateLinks
            v-if="reviews.data.length > 0"
            class="mt-6"
            :modelName="'reviews'"
            :links="reviews.links"
            :from="reviews.from"
            :to="reviews.to"
            :total="reviews.total"
        ></PaginateLinks>
    </div>
</template>
