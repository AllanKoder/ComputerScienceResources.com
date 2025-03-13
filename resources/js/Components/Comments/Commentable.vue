<script setup>
import { ref, computed, watchEffect } from "vue";
import axios from "axios";
import SingleComment from "@/Components/Comments/SingleComment.vue";
import CommentActionsForm from "@/Components/Comments/CommentActionsForm.vue";

const props = defineProps({
    commentable_id: {
        type: Number,
        required: true,
    },
    commentable_type: {
        type: String,
        required: true,
    },
});

// Use Map for faster lookups
const comments = ref([]);
const users = ref(new Map());
const can_load_more_comments = ref(true);
const currentIndex = ref(0);
const isLoading = ref(false);
const error = ref(null);

// Convert API response users to Map
const normalizeUsers = (usersArray) => 
    new Map(usersArray.map(user => [user.id, user]));

// Flatten nested comments for virtual scrolling
const flattenComments = (comments, depth = 0) => {
    return comments.reduce((acc, comment) => {
        acc.push({ ...comment, depth });
        if (comment.children?.length) {
            acc.push(...flattenComments(comment.children, depth + 1));
        }
        return acc;
    }, []);
};

const flattenedComments = computed(() => flattenComments(comments.value));

async function loadComments() {
    if (isLoading.value || !can_load_more_comments.value) return;
    
    isLoading.value = true;
    error.value = null;
    
    try {
        const response = await axios.post(
            route("comments.show", {
                id: props.commentable_id,
                type: props.commentable_type,
                index: currentIndex.value,
            })
        );

        console.log(response);

        if (currentIndex.value === 0) {
            comments.value = response.data.comments;
            users.value = normalizeUsers(response.data.users);
        } else {
            const existingIds = new Set(comments.value.map(c => c.id));
            const newComments = response.data.comments.filter(c => 
                !existingIds.has(c.id)
            );
            
            comments.value = [...comments.value, ...newComments];
            
            // Merge users using Map
            const newUsers = normalizeUsers(response.data.users);
            users.value = new Map([...users.value, ...newUsers]);
        }
        
        can_load_more_comments.value = response.data.has_more_comments;
        currentIndex.value++;
    } catch (err) {
        console.error("Error fetching comments:", err);
        error.value = "Failed to load comments. Please try again later.";
    } finally {
        isLoading.value = false;
    }
}

</script>

<template>
    <div class="comments-section p-4">
        <!-- Error State -->
        <div v-if="error" class="text-red-500 mb-4">{{ error }}</div>

        <!-- Comments List -->
        <template v-if="flattenedComments.length">
            <div 
                v-for="comment in flattenedComments"
                :key="comment.id"
                class="comment-item mb-4 pl-4 border-l border-gray-200"
                :class="{ 'ml-18': comment.depth > 0 }"
            >
                <SingleComment
                    :comment="comment"
                    :commentable_id="commentable_id"
                    :commentable_type="commentable_type"
                    :users="users"
                />
            </div>
        </template>

        <!-- Loading State -->
        <div v-if="isLoading" class="text-center text-gray-500 mb-4">
            Loading comments...
        </div>

        <!-- Load More Button -->
        <button
            v-if="can_load_more_comments && !isLoading"
            @click="loadComments"
            class="w-full py-2 text-center text-blue-500 hover:bg-gray-50 transition-colors"
        >
            View more comments
        </button>

        <!-- New Comment Form -->
        <CommentActionsForm
            label="Add Comment"
            :commentable_id="commentable_id"
            :commentable_type="commentable_type"
            class="mt-4"
        />
    </div>
</template>
