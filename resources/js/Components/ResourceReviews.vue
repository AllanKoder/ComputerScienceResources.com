<script setup>
import { defineProps, ref } from "vue";
import { Icon } from "@iconify/vue";
import CreateResourceReview from "@/Components/Resources/Reviews/CreateResourceReview.vue";
import ResourceReview from "./ResourceReview.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import PaginateLinks from "@/Components/Pagination/PaginateLinks.vue";
import EmptyState from "@/Components/EmptyState.vue";

const props = defineProps({
    reviews: {
        type: Object,
        required: true,
    },
    userReview: {
        type: Object,
        default: null,
    },
    resourceId: {
        type: Number,
        required: true,
    },
    resourceSlug: {
        type: String,
        required: true,
    },
});

const showForm = ref(false);

// Change if is editting or in need of a review
const isEdittingMode = props.userReview !== null;
const textOpen = isEdittingMode ? "Edit your Review" : "Write a Review";
const iconOpen = isEdittingMode ? "mdi:edit" : "mdi:eye";
const iconClose = isEdittingMode ? "mdi:close" : "mdi:eye-off";

</script>

<template>
    <div class="px-6 max-w-7xl mx-auto">
        <!-- Toggle Button -->
        <div class="flex justify-end mb-4">
            <PrimaryButton
                @click="showForm = !showForm"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
            >
                <Icon
                    :icon="showForm ? iconClose : iconOpen"
                    class="text-xl"
                />
                {{ showForm ? "Hide" : textOpen }}
            </PrimaryButton>
        </div>

        <!-- Create a review -->
        <div v-show="showForm" class="mb-8">
            <CreateResourceReview
            :resource-id="props.resourceId"
            :resource-slug="props.resourceSlug"
            :resource-review="props.userReview"
            :is-editing-mode="isEdittingMode"
            />
        </div>

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
