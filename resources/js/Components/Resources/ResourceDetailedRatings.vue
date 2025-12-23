t <script setup>
import { defineProps } from "vue";
import { Icon } from "@iconify/vue";
import StarRating from "@/Components/StarRating/StarRating.vue";

const props = defineProps({
    reviewSummary: {
        type: Object,
        required: true,
    },
});

const ratings = [
    {
        key: "community_rating",
        label: "Community",
        icon: "mdi:account-group",
    },
    {
        key: "teaching_clarity_rating",
        label: "Teaching",
        icon: "mdi:school",
    },
    {
        key: "engagement_rating",
        label: "Engagement",
        icon: "mdi:thumb-up",
    },
    {
        key: "practicality_rating",
        label: "Practicality",
        icon: "mdi:tools",
    },
    {
        key: "user_friendliness_rating",
        label: "User Friendly",
        icon: "mdi:account-heart",
    },
    {
        key: "updates_rating",
        label: "Updates",
        icon: "mdi:update",
    },
];
</script>

<template>
    <div class="flex-1 flex justify-center items-center">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-8">
            <div
                v-for="rating in ratings"
                :key="rating.key"
                class="flex flex-col items-center"
            >
                <label
                    class="flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-gray-100 mb-2"
                >
                    <Icon :icon="rating.icon" class="w-4 h-4" />
                    {{ rating.label }}
                </label>
                <StarRating
                    :model-value="Number(props.reviewSummary?.[rating.key])"
                    :size="20"
                />
            </div>
        </div>
    </div>
</template>
