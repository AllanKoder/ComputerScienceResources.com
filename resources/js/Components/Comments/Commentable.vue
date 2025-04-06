<script setup>
import { ref, provide, readonly, nextTick } from "vue";
import axios from "axios";
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
    commentsCount: {
        type: Number,
        required: true,
    },
});

const users = ref(new Map());
const can_load_more_comments = ref(true);
const currentIndex = ref(0);
const isLoading = ref(false);
const error = ref(null);
const commentsLeft = ref(props.commentsCount);
const idToChildren = ref(new Map());

const createdNewComment = (newComment, userData) => {
    console.log("Created a new comment!", newComment, userData);
    updateUsers([userData]);
    updateCommentHierarchy([newComment]);

    // Ensure DOM updates are complete, then scroll to the new comment
    nextTick(() => {
        const newCommentElement = document.getElementById(
            "comment_" + newComment.id
        );
        if (newCommentElement) {
            newCommentElement.scrollIntoView({
                behavior: "smooth",
                block: "center",
                inline: "nearest",
            });
        }
    });
};

provide("commentableId", props.commentableId);
provide("commentableType", props.commentableType);
provide("users", readonly(users));
provide("createdNewComment", createdNewComment);

function updateUsers(newUsers) {
    // Convert API response users to Map
    const normalizeUsers = (usersArray) =>
        new Map(usersArray.map((user) => [user.id, user]));

    // Setting the map to a new value
    if (currentIndex.value === 0) {
        users.value = normalizeUsers(newUsers);
    } else {
        users.value = new Map([...users.value, ...normalizeUsers(newUsers)]);
    }
}

function updateCommentHierarchy(newComments) {
    const hierarchyUpdates = {};

    commentsLeft.value -= newComments.length;

    newComments.forEach((comment) => {
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
                [...(idToChildren.value[parentId] || []), ...children],
            ])
        ),
    };
}

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

        // Update the users
        updateUsers(response.data.users);
        // Update comment hierarchy for both new and existing comments
        updateCommentHierarchy(response.data.comments);

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
        <CommentList :id-to-children="idToChildren" />

        <!-- Loading State -->
        <div v-if="isLoading" class="text-center text-gray-500 mb-4">
            Loading comments...
        </div>

        <!-- Load More Button -->
        <div v-if="commentsLeft > 0">
            <button
                v-if="can_load_more_comments && !isLoading"
                @click="loadComments"
                class="w-full py-2 text-center text-blue-500 hover:bg-gray-50 transition-colors"
            >
                View {{ commentsLeft }} Comments
            </button>
        </div>

        <!-- New Comment Form -->
        <CommentActionsForm label="Add Comment" class="mt-4" />
    </div>
</template>
