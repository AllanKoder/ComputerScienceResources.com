<script setup>
import { ref } from "vue";
import { Icon } from "@iconify/vue";
import SingleComment from "@/Components/Comments/SingleComment.vue";

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

// Comment structure is a list of:
// comments: {
//  parent_comment_id
//  content
// }

// The single commment is to handle user name, comment content
const comments = ref();

async function testFunction() {
    try {
        const response = await axios.post(
            route("comments.show", { id: props.commentable_id, type: props.commentable_type, index: 0 })
        );
        console.log(response.data);
    } catch (error) {
        console.error("Error fetching comments:", error);
    }
}

testFunction();

</script>

<template>
    
    <SingleComment :commentable_id="commentable_id" :commentable_type="commentable_type"></SingleComment>
</template>
