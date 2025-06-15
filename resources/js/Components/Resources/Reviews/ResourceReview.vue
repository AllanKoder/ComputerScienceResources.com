<script setup>
import { defineProps, ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { ratingLabels } from "@/Helpers/labels";
import StarRating from "@/Components/StarRating/StarRating.vue";
import Commentable from "@/Components/Comments/Commentable.vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import UpdateResourceReview from "@/Components/Resources/Reviews/UpdateResourceReview.vue";
import { Icon } from "@iconify/vue";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import ProfilePhoto from "@/Components/ProfilePhoto.vue";
import {formatDate} from "@/Helpers/dates";

const props = defineProps({
    review: Object,
});

const page = usePage();

const isOwner = computed(() => {
    return page.props.auth?.user?.id === props.review.user_id;
});

const editing = ref(false);
const showCancelConfirm = ref(false);

const toggleEdit = () => {
    editing.value = true;
};

const requestCancelEdit = () => {
    showCancelConfirm.value = true;
};

const confirmCancelEdit = () => {
    editing.value = false;
    showCancelConfirm.value = false;
};

const cancelDialog = () => {
    showCancelConfirm.value = false;
};

const ratingFeatures = Object.entries(ratingLabels).map(([key, label]) => ({
    key,
    label,
}));
</script>

<template>
    <div>
        <!-- Edit Mode -->
        <div v-if="editing">
            <div class="flex justify-end mb-2">
                <Button
                label="Cancel Edit"
                severity="secondary"
                @click="requestCancelEdit"
                />
            </div>
            <UpdateResourceReview
            :resource-id="props.review.computer_science_resource_id"
            :resource-review="props.review"
            />
        </div>
        <!-- Normal View Mode -->
        <div v-else>
            <div class="flex justify-between items-center">
                <div class="flex-row flex gap-4">
                    <Upvotable
                        :upvotable-key="'review'"
                        :upvotable-id="props.review.id"
                        :initial-votes="props.review.vote_score"
                        :user-vote="props.review.user_vote"
                        />

                    <h3 class="text-xl font-semibold my-auto">{{ review.title }}</h3>
                </div>

                <div class="flex items-center gap-2">
                    <span class="font-medium">Rating:</span>
                    <div class="flex-row flex">
                    <StarRating
                        :model-value="review.average_score"
                        :size="24"
                    />
                </div>

                    <button
                        v-if="isOwner"
                        @click="toggleEdit"
                        class="ml-2 text-gray-500 hover:text-blue-600"
                    >
                        <Icon icon="mdi:pencil" class="text-xl" />
                    </button>
                </div>
            </div>

            <p class="text-gray-700 mb-4 whitespace-pre-line mt-2">
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

            <div class="flex-row flex w-full justify-end">
                <ProfilePhoto
                :src="review.user.profile_photo_url"
                :alt="'User Avator'"
                />
                <p class="text-sm my-auto text-gray-800 truncate">
                    {{ review.user.name }}
                </p>
                <time :datetime="review.created_at"
                    class="text-sm text-gray-500 my-auto"
                    :title="review.created_at"
                    >
                    {{ formatDate(review.created_at) }}
                </time>
            </div>

            <!-- Detailed Ratings -->
            <div class="flex flex-wrap gap-4 mt-4 justify-between">
                <div
                    v-for="feature in ratingFeatures"
                    :key="feature.key"
                    class="flex flex-col items-center"
                >
                    <span class="font-semibold mb-1">{{ feature.label }}</span>
                    <StarRating
                        :model-value="review[feature.key]"
                        :size="20"
                    />
                </div>
            </div>

            <Commentable
                :commentable-id="review.id"
                :commentable-key="'review'"
                :comments-count="review.comments_count"
            />
        </div>

        <!-- Confirmation Dialog -->
        <Dialog
            v-model:visible="showCancelConfirm"
            modal
            header="Cancel Editing?"
            :style="{ width: '25rem' }"
        >
            <span class="block mb-6"
                >Are you sure you want to cancel editing this review? Changes will not be saved.</span
            >
            <div class="flex justify-end gap-2">
                <Button label="No" severity="secondary" @click="cancelDialog" />
                <Button
                    label="Yes"
                    severity="danger"
                    @click="confirmCancelEdit"
                />
            </div>
        </Dialog>
    </div>
</template>
