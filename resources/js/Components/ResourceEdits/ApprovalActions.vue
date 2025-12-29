<script setup>
import { Icon } from "@iconify/vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";

defineProps({
    editedResource: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="mt-8 border-t border-gray-200 dark:border-gray-800 pt-6">
        <div class="flex justify-center">
            <Upvotable
                :flexRow="true"
                :upvotable-key="'edit'"
                :upvotable-id="editedResource.id"
                :initial-votes="editedResource.vote_score"
                :user-vote="editedResource.user_vote"
                :refresh="true"
                class="flex items-center gap-6"
            >
                <!-- Downvote (Reject) Button -->
                <template #alreadyDownvotedIcon>
                    <span
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg font-medium"
                    >
                        <Icon
                            icon="mdi:close-circle"
                            class="w-4 h-4 mr-2"
                        />
                        Rejected
                    </span>
                </template>
                <template #downvoteIcon>
                    <span
                        class="inline-flex items-center px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg font-medium transition-colors cursor-pointer"
                    >
                        <Icon
                            icon="mdi:close-circle-outline"
                            class="w-4 h-4 mr-2"
                        />
                        Reject Changes
                    </span>
                </template>

                <!-- Vote Count -->
                <template #votes="{ votes }">
                    <div class="flex flex-col items-center">
                        <span
                            class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                            >{{ votes }}</span
                        >
                        <span class="text-sm text-gray-600 dark:text-gray-300">
                            Approval{{ votes === 1 ? "" : "s" }}
                        </span>
                    </div>
                </template>

                <!-- Upvote (Approve) Button -->
                <template #alreadyUpvotedIcon>
                    <span
                        class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg font-medium"
                    >
                        <Icon
                            icon="mdi:check-circle"
                            class="w-4 h-4 mr-2"
                        />
                        Approved!
                    </span>
                </template>
                <template #upvoteIcon>
                    <span
                        class="inline-flex items-center px-4 py-2 bg-green-200 hover:bg-green-300 text-green-800 border border-green-300 rounded-lg font-medium transition-colors cursor-pointer"
                    >
                        <Icon
                            icon="mdi:check-circle-outline"
                            class="w-4 h-4 mr-2"
                        />
                        Approve Changes
                    </span>
                </template>
            </Upvotable>
        </div>
    </div>
</template>
