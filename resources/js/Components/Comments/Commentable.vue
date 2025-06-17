<script setup>
import { ref, provide, readonly, nextTick, onMounted } from "vue";
import axios from "axios";
import CommentActionsForm from "@/Components/Comments/CommentActionsForm.vue";
import SortByDropdown from "@/Components/Comments/SortUpvotesByDropdown.vue";
import CommentList from "./CommentList.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import LoadingAnimation from "@/Components/LoadingAnimation.vue";
import { Icon } from "@iconify/vue";

const props = defineProps({
    commentableId: {
        type: Number,
        required: true,
    },
    commentableKey: {
        type: String,
        required: true,
    },
    commentsCount: {
        type: Number,
        required: true,
    },
    paginationLimit: {
        type: Number,
        default: -1,
    },
    sortByInitialValue: {
        type: String,
        default: "top"
    },
    loadedCommentData: {
        type: Object,
        required: false,
        default: null,
    },
    hasSortByDropdown: {
        type: Boolean,
        default: true,
    },
});

const hasLoadedCommentData = props.loadedCommentData != null;
const usersMap = ref(new Map());
const canLoadMoreComments = ref(true);
const currentIndex = ref(0);
const isLoading = ref(false);
const error = ref(null);
const commentsLeft = ref(props.commentsCount);
const idToChildren = ref(new Map());
const sortBy = ref(props.sortByInitialValue);
const hasOpenedComments = ref(false);

const createdNewCommentCallback = (newComment, userData) => {
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
provide("commentableKey", props.commentableKey);
provide("users", readonly(usersMap));
provide("createdNewCommentCallback", createdNewCommentCallback);

function updateUsers(newUsers) {
    //normalize: if it’s a `{ data: { … } }` wrapper, grab `.data`
    const users = newUsers.map((u) => u.data ?? u);

    // Convert API response users to Map
    const normalizeUsers = (usersArray) =>
        new Map(usersArray.map((user) => [user.id, user]));

    // Setting the map to a new value
    if (currentIndex.value === 0) {
        usersMap.value = normalizeUsers(users);
    } else {
        usersMap.value = new Map([...usersMap.value, ...normalizeUsers(users)]);
    }
}

function updateCommentHierarchy(newComments) {
    //normalize: if it’s a `{ data: { … } }` wrapper, grab `.data`
    const comments = newComments.map((c) => c.data ?? c);

    commentsLeft.value -= comments.length;

    const hierarchyUpdates = {};
    comments.forEach((comment) => {
        // now comment.parent_comment_id is either a number or null
        const parentId = comment.parent_comment_id;

        if (!hierarchyUpdates[parentId]) {
            hierarchyUpdates[parentId] = [];
        }
        hierarchyUpdates[parentId].push(comment);
    });

    // merge into your reactive map/object
    idToChildren.value = {
        ...idToChildren.value,
        ...Object.fromEntries(
            Object.entries(hierarchyUpdates).map(([pid, children]) => [
                pid,
                [...(idToChildren.value[pid] || []), ...children],
            ])
        ),
    };
}

function addCommentData(commentData) {
    // Update the users
    updateUsers(commentData.users);
    // Update comment hierarchy for both new and existing comments
    updateCommentHierarchy(commentData.comments);

    canLoadMoreComments.value = commentData.has_more_comments;
    currentIndex.value++;
}

async function loadComments() {
    if (isLoading.value || !canLoadMoreComments.value) return;

    isLoading.value = true;
    error.value = null;
    hasOpenedComments.value = true;

    try {
        const response = await axios.get(
            route("comments.show", {
                commentableId: props.commentableId,
                commentableKey: props.commentableKey,
                index: currentIndex.value,
                paginationLimit: props.paginationLimit,
                sort_by: sortBy.value,
            })
        );

        console.log(response.data);
        console.log(props.loadedCommentData);

        // Update the users
        addCommentData(response.data);
    } catch (err) {
        console.error("Error fetching comments:", err);
        error.value = "Failed to load comments. Please try again later.";
    } finally {
        isLoading.value = false;
    }
}

function handleSortChange(newSortType) {
    if (newSortType == sortBy.value) return;

    sortBy.value = newSortType;

    usersMap.value = new Map();
    canLoadMoreComments.value = true;
    isLoading.value = false;
    error.value = null;
    commentsLeft.value = props.commentsCount;
    idToChildren.value = new Map();

    currentIndex.value = 0;
    loadComments();
}

onMounted(() => {
    if (hasLoadedCommentData) {
        addCommentData(props.loadedCommentData);
    }
});
</script>

<template>
    <div class="comments-section bg-white rounded-lg p-1">
        <SortByDropdown
            v-if="props.hasSortByDropdown && hasOpenedComments"
            @change="handleSortChange"
        ></SortByDropdown>

        <!-- Error State -->
        <div v-if="error" class="text-red-500 mb-4">{{ error }}</div>

        <!-- Comments List -->
        <CommentList :id-to-children="idToChildren" />

        <!-- Loading State -->
        <div v-if="isLoading" class="my-8">
            <LoadingAnimation />
        </div>

        <!-- Load More Button -->
        <div v-if="commentsLeft > 0">
            <button
                v-if="canLoadMoreComments && !isLoading"
                @click="loadComments"
                class="w-full py-2 text-center text-primary hover:bg-background/50 transition-colors"
            >
                View {{ commentsLeft }} Comments
            </button>
        </div>

        <!-- New Comment Form -->
        <CommentActionsForm label="Add Comment" class="mt-4" />
    </div>
</template>
