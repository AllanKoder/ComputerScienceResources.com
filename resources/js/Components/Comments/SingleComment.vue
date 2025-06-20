<script setup>
import { inject } from "vue";
import CommentActionsForm from "@/Components/Comments/CommentActionsForm.vue";
import Upvotable from "../Upvote/Upvotable.vue";
import { getConfigData } from "@/Helpers/config";
import UserProfile from "@/Components/Profile/UserProfile.vue";

const props = defineProps({
    comment: {
        type: Object,
        required: true,
    },
    depth: {
        type: Number,
        default: 1,
    },
});

const users = inject("users");

</script>

<template>
    <div
        class="py-4 border-b border-gray-300 flex flex-row gap-4 w-full"
        :key="comment.id"
        :id="'comment_' + comment.id"
    >
        <Upvotable
            :upvotable-key="'comment'"
            :upvotable-id="comment.id"
            :initial-votes="comment.vote_score"
            :user-vote="comment.user_vote"
        ></Upvotable>

        <div>
            <!-- User Info with Lazy Loading -->
            <div class="flex items-center space-x-2">
                <UserProfile
                    :user="users.get(comment.user_id)"
                    :date="comment.created_at"
                />
            </div>

            <!-- Comment Content -->
            <p class="mt-2 text-gray-700 break-words">{{ comment.content }}</p>

            <!-- Actions Form -->
            <CommentActionsForm
                v-if="depth <= getConfigData().COMMENT_MAX_DEPTH"
                :key="`actions-${comment.id}`"
                :parent-comment-id="comment.id"
                class="mt-2"
            />
        </div>
    </div>
</template>
