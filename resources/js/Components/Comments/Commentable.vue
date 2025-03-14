<script setup>
import { ref, computed, watchEffect } from "vue";
import axios from "axios";
import SingleComment from "@/Components/Comments/SingleComment.vue";
import CommentActionsForm from "@/Components/Comments/CommentActionsForm.vue";
import CommentList from "./CommentList.vue";

const props = defineProps({
    commentableId: {
        type: Number,
        required: true,
    },
    commentableType: {
        type: String,
        required: true,
    },
});

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

const idToChildren = ref(new Map());
function updateCommentHierarchy(newComments) {
    const hierarchyUpdates = {};
    
    newComments.forEach(comment => {
        const parentId = comment.parent_comment_id; 
        if (!hierarchyUpdates[parentId]) {
            hierarchyUpdates[parentId] = [];
        }
        hierarchyUpdates[parentId].push(comment);
    });

    // Merge updates into idToChildren with reactivity
    idToChildren.value = {
        ...idToChildren.value,
        ...Object.fromEntries(
            Object.entries(hierarchyUpdates).map(([parentId, children]) => [
                parentId,
                [...(idToChildren.value[parentId] || []), ...children]
            ])
        )
    };
}

const flattenedComments = computed(() => flattenComments(comments.value));

async function loadComments() {
    if (isLoading.value || !can_load_more_comments.value) return;
    
    isLoading.value = true;
    error.value = null;
    
    try {
        const response = await axios.post(
            route("comments.show", {
                id: props.commentableId,
                type: props.commentableType,
                index: currentIndex.value,
            })
        );

        console.log(response);


        const newComments = response.data.comments.filter(
            c => !comments.value.some(existing => existing.id === c.id)
        );

        if (currentIndex.value === 0) {
            comments.value = newComments;
            users.value = normalizeUsers(response.data.users);
        } else {
            comments.value = [...comments.value, ...newComments];
            users.value = new Map([...users.value, ...normalizeUsers(response.data.users)]);
        }

        // Update comment hierarchy for both new and existing comments
        updateCommentHierarchy(newComments);
        
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
        <CommentList 
            :id-to-children="idToChildren"
            :commentableId="props.commentableId"
            :commentableType="props.commentableType"
            :users="users"
        />

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
            :commentableId="props.commentableId"
            :commentableType="props.commentableType"
            class="mt-4"
        />
    </div>
</template>
