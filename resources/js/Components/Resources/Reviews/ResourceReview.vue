<script setup>
import { defineProps } from "vue";
import { ratingLabels } from "@/Helpers/labels";
import StarRating from "@/Components/StarRating/StarRating.vue";
import Commentable from "@/Components/Comments/Commentable.vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import UserProfile from "@/Components/Profile/UserProfile.vue";

const props = defineProps({
    review: Object,
});

const ratingFeatures = Object.entries(ratingLabels).map(([key, label]) => ({
    key,
    label,
}));
</script>

<template>
    <div class="flex flex-col sm:flex-row justify-between items-center gap-1 sm:gap-0">
        <div class="flex flex-row gap-4 items-center w-full sm:w-auto">
            <Upvotable
                :upvotable-key="'review'"
                :upvotable-id="props.review.id"
                :initial-votes="props.review.vote_score"
                :user-vote="props.review.user_vote"
            />

            <h3 class="text-xl font-semibold my-auto">
                {{ review.title }}
            </h3>
        </div>

        <div class="flex items-center gap-2 mt-2 sm:mt-0 w-full sm:w-auto justify-end">
            <span class="font-medium">Rating:</span>
            <div class="flex flex-row">
                <StarRating
                    :model-value="review.average_score"
                    :size="24"
                />
            </div>
        </div>
    </div>

    <UserProfile
        class="my-2"
        :user="review.user"
        :date="review.created_at"
    />

    <p class="text-gray-700 mb-4 whitespace-pre-line mt-1">
        {{ review.description }}
    </p>

    <div class="flex flex-col sm:flex-row justify-between">
        <div class="sm:w-1/2 mb-4 sm:mb-0">
            <h4 class="font-semibold mb-2">Pros</h4>
            <ul class="list-disc pl-5">
                <li
                    v-for="pro in review.pros"
                    :key="pro"
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
                    :key="con"
                    class="text-gray-600"
                >
                    {{ con }}
                </li>
            </ul>
        </div>
    </div>

    <!-- Detailed Ratings -->
    <div class="flex flex-wrap gap-4 mt-2 justify-between">
        <div
            v-for="feature in ratingFeatures"
            :key="feature.key"
            class="flex flex-col items-center"
        >
            <span class="font-semibold mb-1">{{ feature.label }}</span>
            <StarRating :model-value="review[feature.key]" :size="20" />
        </div>
    </div>

    <Commentable
        :commentable-id="review.id"
        :commentable-key="'review'"
        :comments-count="review.comments_count"
    />
</template>
