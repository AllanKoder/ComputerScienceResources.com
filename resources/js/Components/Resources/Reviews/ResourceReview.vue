<script setup>
import { defineProps } from "vue";
import { ratingLabels } from "@/Helpers/labels";
import Rating from "primevue/rating";
import Commentable from "@/Components/Comments/Commentable.vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";

const props = defineProps({
    review: Object,
});

// Convert ratingLabels object to array for iteration in template
const ratingFeatures = Object.entries(ratingLabels).map(([key, label]) => ({
    key,
    label,
}));
</script>

<template>
    <Upvotable
        :upvotable-type="'review'"
        :upvotable-id="props.review.id"
        :initial-votes="props.review.vote_score"
        :user-vote="props.review.user_vote"
    ></Upvotable>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold">{{ review.title }}</h3>
        <div class="flex items-center">
            <span class="mr-2 font-medium">Rating:</span>
            <Rating :model-value="review.average_score" readonly :cancel="false" />
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
                    v-for="pro in review.pros"
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
                    v-for="con in review.cons"
                    class="text-gray-600"
                >
                    {{ con }}
                </li>
            </ul>
        </div>
    </div>
    <!-- Detailed Ratings in Flex Layout -->
    <div class="flex flex-wrap gap-4 mt-4 justify-between">
        <div
            v-for="feature in ratingFeatures"
            :key="feature.key"
            class="flex flex-col items-center"
        >
            <span class="font-semibold mb-1">{{ feature.label }}</span>
            <Rating
                :model-value="review[feature.key]"
                readonly
                :cancel="false"
            />
        </div>
    </div>

    <Commentable 
        :commentable-id="review.id" 
        :commentable-type="'review'"
        :comments-count="review.comments_count"
    />
</template>
