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
// A reactive list for users returned by the API.
const users = ref([]);
// Flag indicating if more comments are available.
const can_load_more_comments = ref(true);
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

        console.log(response);

        // For index 0, replace; for later pages, append.
        if (currentIndex.value === 0) {
            comments.value = response.data.comments;
            users.value = response.data.users;
        } else {
            const newComments = response.data.comments.filter((comment) => {
                return !comments.value.some(
                    (existingComment) => existingComment.id === comment.id
                );
            });
            comments.value = comments.value.concat(newComments);

            // Merge users from the new page into the existing ones (deduping by id)
            response.data.users.forEach((newUser) => {
                if (!users.value.some((u) => u.id === newUser.id)) {
                    users.value.push(newUser);
                }
            });
        }
        can_load_more_comments.value = response.data.has_more_comments;
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
            <!-- Render each comment with the user data passed down -->
            <SingleComment
                :comment="comment"
                :commentable_id="commentable_id"
                :commentable_type="commentable_type"
                :users="users"
            />
        </div>
        <button
            v-if="can_load_more_comments"
            @click="loadMoreComments"
            class="btn btn-link mt-2"
        >
            View more comments
        </button>

        <CommentActionsForm
            label="Comment"
            :commentable_id="commentable_id"
            :commentable_type="commentable_type"
        />
    </div>
</template>
