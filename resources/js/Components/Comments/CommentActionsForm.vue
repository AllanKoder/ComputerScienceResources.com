<script setup>
import { ref, reactive, inject } from "vue";
import { Icon } from "@iconify/vue";
import axios from "axios";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextArea from "@/Components/TextArea.vue";

const props = defineProps({
    parentCommentId: {
        type: Number,
        default: null,
        required: false,
    },
    label: {
        type: String,
        default: "Reply",
        required: false,
    },
});

const isOpen = ref(false);

const commentableKey = inject("commentableKey");
const commentableId = inject("commentableId");
const createdNewCommentCallback = inject("createdNewCommentCallback");

const toggleOpen = () => {
    isOpen.value = !isOpen.value;
};

const form = reactive({
    content: "",
    commentable_id: commentableId,
    commentable_key: commentableKey,
    parent_comment_id: props.parentCommentId ?? null,
    errors: {},
    processing: false,
});

const submit = () => {
    form.processing = true;
    form.errors = {};
    axios
        .post(route("comments.store"), form)
        .then((response) => {
            console.log("Successful comment post!");

            // Notify the parent that a comment is made
            createdNewCommentCallback(response.data.new_comment, response.data.user);

            form.content = "";
            isOpen.value = false;
        })
        .catch((error) => {
            if (
                error.response &&
                error.response.data &&
                error.response.data.errors
            ) {
                form.errors = error.response.data.errors;
            }
            console.warn("Failed to post comment:", error);
        })
        .finally(() => {
            form.processing = false;
        });
};
</script>

<template>
    <div>
        <div class="mt-2 flex items-center space-x-4">
            <button
                @click="toggleOpen"
                class="text-sm text-primary hover:underline"
            >
                <Icon icon="mdi:comment-outline" class="w-5 h-5 inline-block" />
                {{ label }}
            </button>
        </div>

        <form v-if="isOpen" @submit.prevent="submit" class="space-y-2 mt-2">
            <TextArea
                v-model="form.content"
                placeholder="Write your comment..."
                :rows="4"
            />
            <div>
                <PrimaryButton
                    :disabled="form.processing"
                    class="flex items-center gap-2"
                >
                    <Icon icon="mdi:send" class="w-4 h-4" />
                    Submit
                </PrimaryButton>
            </div>
            <div v-if="form.errors.content" class="text-red-500 text-sm">
                {{ form.errors.content }}
            </div>
        </form>
    </div>
</template>
