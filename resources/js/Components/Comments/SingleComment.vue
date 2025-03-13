<script setup>
import { computed } from "vue";
import CommentActionsForm from "@/Components/Comments/CommentActionsForm.vue";

const props = defineProps({
    comment: {
        type: Object,
        required: true,
    },
    commentable_id: {
        type: Number,
        required: true,
    },
    commentable_type: {
        type: String,
        required: true,
    },
    users: {
        type: Array,
        required: true,
    },
});

// Look up the user data from the passed-in users array.
const user = computed(() => props.users.find(u => u.id === props.comment.user_id));
</script>

<template>
    <div class="p-4 border-b border-gray-200">
        <!-- User Info -->
        <div class="flex items-center space-x-2">
            <img
                :src="user.profile_photo_url"
                alt="User Avatar"
                class="w-8 h-8 rounded-full"
            />
            <span class="font-semibold text-gray-800">{{ user.name }}</span>
            <span class="text-sm text-gray-500">
                {{ new Date(comment.created_at).toLocaleString() }}
            </span>
        </div>

        <!-- Comment Content -->
        <p class="mt-2 text-gray-700">{{ comment.content }}</p>

        <!-- Comment Actions Form -->
        <CommentActionsForm
            :commentable_id="commentable_id"
            :commentable_type="commentable_type"
            :comment="comment"
        />
    </div>
</template>
