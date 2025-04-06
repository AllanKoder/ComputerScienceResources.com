<script setup>
import { defineProps, ref } from "vue";
import CreateResourceReview from "./CreateResourceReview.vue";
import ResourceReview from "./ResourceReview.vue";

const props = defineProps({
    reviews: {
        type: Array,
        required: true,
    },
    resourceId: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <div class="p-6 max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-8">Resource Reviews</h2>

        <!-- Create a review -->
        <CreateResourceReview
            :resource-id="props.resourceId"
        ></CreateResourceReview>

        <div class="space-y-6">
            <div
                v-for="review in reviews"
                :key="review.id"
                class="bg-white/70 backdrop-blur-md p-6 rounded-lg shadow-md"
            >
                <ResourceReview :review="review"></ResourceReview>
            </div>
        </div>
    </div>
</template>
