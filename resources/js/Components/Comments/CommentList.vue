<script setup>
import { defineProps } from "vue";
import SingleComment from "./SingleComment.vue";

const props = defineProps({
    idToChildren: {
        required: true,
        type: Object,
    },
    commentableId: {
        type: Number,
        required: true,
    },
    commentableType: {
        type: String,
        required: true,
    },
    users: {
        type: Map,
        required: true,
    },
    id: {
        default: null,
        type: Number,
    },
    depth: { // circuit breaker in case of a bad recursion
        default: 0,
        type: Number,
    },
});
</script>

<template>
    <div class="comments-list ml-4">
        <div v-for="comment in idToChildren[id] || []" :key="comment.id">

            <SingleComment
                :comment="comment"
                :commentableId="props.commentableId"
                :commentableType="props.commentableType"
                :users="props.users"
            />

            <CommentList
                v-if="depth <= 7 && comment"
                :depth="depth + 1"
                :id="comment.id"
                :idToChildren="idToChildren"
                :commentableId="props.commentableId"
                :commentableType="props.commentableType"
                :users="props.users"
            />
        </div>
    </div>
</template>
