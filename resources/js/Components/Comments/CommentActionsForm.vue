<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Icon } from "@iconify/vue";

const props = defineProps({
    label: {
        type: String,
        default: "Reply",
        required: false
    },
    comment_parent_id: {
        type: Number,
        default: null,
        required: false,
    },
    commentable_id: {
        type: Number,
        required: true,
    },
    commentable_type: {
        type: String,
        required: true,
    },
});

const isOpen = ref(false);

const toggleOpen = () => {
    isOpen.value = !isOpen.value;
};

// Setup form with Inertia
const form = useForm({
    content: "",
    commentable_id: props.commentable_id,
    commentable_type: props.commentable_type,
    parent_comment_id: props.comment_parent_id ?? null,
});

// Submit form handler
const submit = () => {
    form.post(route("comments.store"), {
        preserveScroll: true,
        onFailure: () => {
            console.warn("Failed to post comment: " + form);
        },
        onSuccess: () => {
            form.reset("content");
            console.log("Successful comment post!");
        },
    });
};
</script>

<template>
    <div>
        <!-- Comment Actions -->
        <div class="mt-2 flex items-center space-x-4">
            <button @click="toggleOpen" class="text-sm text-blue-500 hover:underline">
                <Icon icon="mdi:comment-outline" class="w-5 h-5 inline-block" /> {{ label }}
            </button>
        </div>

        <!-- Comment Form -->
        <form v-if="isOpen" @submit.prevent="submit" class="space-y-4 mt-2">
            <textarea
                v-model="form.content"
                class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring focus:border-blue-300"
                placeholder="Write your comment..."
                rows="4"
            ></textarea>
            <div>
                <button
                    type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded disabled:opacity-50"
                    :disabled="form.processing"
                >
                    Submit
                </button>
            </div>
            <div v-if="form.errors.content" class="text-red-500 text-sm">
                {{ form.errors.content }}
            </div>
        </form>
    </div>
</template>
