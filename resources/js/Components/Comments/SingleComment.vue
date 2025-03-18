<script setup>
import { computed, inject } from "vue";
import CommentActionsForm from "@/Components/Comments/CommentActionsForm.vue";

const props = defineProps({
    comment: {
        type: Object,
        required: true,
    }
});

const users = inject('users');

// Memoize date formatting
const formattedDate = computed(() => 
    new Date(props.comment.created_at).toLocaleString(navigator.language, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
);
</script>

<template>
    <div class="p-4 border-b border-gray-200" :key="comment.id" :id="'comment_'+comment.id">
        <!-- User Info with Lazy Loading -->
        <div class="flex items-center space-x-2">
            <img
                :src="users.get(comment.user_id)?.profile_photo_url"
                alt="User avatar"
                class="w-8 h-8 rounded-full"
                loading="lazy"
                width="32"
                height="32"
            />
            <div class="min-w-0">
                <p class="font-semibold text-gray-800 truncate">{{ users.get(comment.id)?.name }}</p>
                <time 
                    :datetime="comment.created_at"
                    class="text-sm text-gray-500"
                    :title="comment.created_at"
                >
                    {{ formattedDate }}
                </time>
            </div>
        </div>

        <!-- Comment Content -->
        <p class="mt-2 text-gray-700 break-words">{{ comment.content }}</p>

        <!-- Actions Form -->
        <CommentActionsForm
            :key="`actions-${comment.id}`"
            :parentCommentId="comment.id"
            class="mt-2"
        />
    </div>
</template>
