<script setup>
import { ref, onMounted } from "vue";
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

// A reactive list of top-level comments.
const comments = ref([]);
// Pagination index for loading additional top-level comments.
const currentIndex = ref(0);

async function loadComments() {
    try {
        // Call your backend to get a page of comments.
        // Make sure your backend route accepts { id, type, index }.
        const response = await axios.post(
            route("comments.show", {
                id: props.commentable_id,
                type: props.commentable_type,
                index: currentIndex.value,
            })
        );
        // For index 0, replace; for later pages, append.
        if (currentIndex.value === 0) {
            comments.value = response.data;
        } else {
            comments.value = comments.value.concat(response.data);
        }
    } catch (error) {
        console.error("Error fetching comments:", error);
    }
}

function loadMoreComments() {
    loadComments();
    currentIndex.value++;
}
</script>

<template>
    <div class="comments-section">
        <div
            v-for="comment in comments"
            :key="comment.id"
            class="comment"
            :style="{ marginLeft: `${comment.depth * 20}px` }"
        >
            <!-- Render each comment with indentation based on depth -->
            <SingleComment
                :comment="comment"
                :commentable_id="commentable_id"
                :commentable_type="commentable_type"
            />
        </div>
        <button @click="loadMoreComments" class="btn btn-link mt-2">
            View more comments
        </button>

        <CommentActionsForm
            label="Comment"
            :commentable_id="props.commentable_id"
            :commentable_type="props.commentable_type"
        />
    </div>
</template>
