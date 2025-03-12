<script setup>
import { defineProps } from "vue";
import { ratingLabels } from "@/Helpers/constants";
import Rating from "primevue/rating";
import Commentable from "@/Components/Comments/Commentable.vue";
import { ref } from 'vue'

const props = defineProps({
    review: Object,
});


// Track which reviews are expanded
const expandedReviews = ref({});

const toggleExpand = (id) => {
    expandedReviews.value[id] = !expandedReviews.value[id];
};

// Convert ratingLabels object to array for iteration in template
const ratingFeatures = Object.entries(ratingLabels).map(([key, label]) => ({
    key,
    label,
}));
</script>

<template>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold">{{ review.title }}</h3>
        <div class="flex items-center">
            <span class="mr-2 font-medium">Rating:</span>
            <Rating :modelValue="4.5" readonly :cancel="false" />
        </div>
    </div>
    <p class="text-gray-700 mb-4 whitespace-pre-line">
        {{ review.description }}
    </p>
    <div class="flex flex-col sm:flex-row justify-between">
        <div class="sm:w-1/2 mb-4 sm:mb-0">
            <h4 class="font-semibold mb-2">Pros</h4>
            <ul class="list-disc pl-5">
                <li
                    v-for="(pro, index) in JSON.parse(review.pros)"
                    :key="index"
                    class="text-gray-600"
                >
                    {{ pro }}
                </li>
            </ul>
        </div>
        <div class="sm:w-1/2">
            <h4 class="font-semibold mb-2">Cons</h4>
            <ul class="list-disc pl-5">
                <li
                    v-for="(con, index) in JSON.parse(review.cons)"
                    :key="index"
                    class="text-gray-600"
                >
                    {{ con }}
                </li>
            </ul>
        </div>
    </div>
    <!-- Toggle for Detailed Ratings -->
    <div class="mt-4">
        <button
            @click="toggleExpand(review.id)"
            class="text-blue-500 underline"
        >
            {{ expandedReviews[review.id] ? "Hide Details" : "Show Details" }}
        </button>
    </div>
    <!-- Detailed Ratings in Flex Layout -->
    <div v-if="expandedReviews[review.id]" class="mt-4 border-t pt-4">
        <div class="flex flex-wrap gap-4 justify-between">
            <div
                v-for="feature in ratingFeatures"
                :key="feature.key"
                class="flex flex-col items-center"
            >
                <span class="font-semibold mb-1">{{ feature.label }}</span>
                <Rating
                    :modelValue="review[feature.key]"
                    readonly
                    :cancel="false"
                />
            </div>
        </div>
    </div>

    <Commentable :commentable_id="review.id" :commentable_type="'review'"></Commentable>
</template>
