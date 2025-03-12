<script setup>
import { ref } from "vue";
import { Icon } from "@iconify/vue";
import CommentForm from "@/Components/Comments/CommentForm.vue";

const props = defineProps({
    commentable_id: {
        type: Number,
        required: true,
    },
    commentable_type: {
        type: String,
        required: true,
    },
    parent_comment_id: {
        type: Number,
        required: false,
        default: null,
    },
});

// Toggle state for the comment bar
const isOpen = ref(false);
const toggleOpen = () => {
    isOpen.value = !isOpen.value;
};
</script>

<template>
    <div class="mt-4">
        <!-- Iconify comment button to toggle the comment bar -->
        <button
            @click="toggleOpen"
            class="flex items-center space-x-2 focus:outline-none"
        >
            <Icon
                icon="mdi:comment-outline"
                class="w-6 h-6 text-gray-600 hover:text-gray-800"
            />
            <span class="text-sm text-gray-600">Comment</span>
        </button>

        <CommentForm
            v-if="isOpen"
            :commentable_id="commentable_id"
            :commentable_type="commentable_type"
            :parent_comment_id="parent_comment_id"
        />
    </div>
</template>
