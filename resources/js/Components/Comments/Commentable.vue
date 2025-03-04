<script setup>
import { ref } from "vue";
import { Icon } from "@iconify/vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    commentable_id: {
        type: Number,
        required: true,
    },
    commentable_type: {
        type: String,
        required: true,
    },
    parent_comment_id: {
        type: Number,
        required: false,
    },
});

// Toggle state for the comment bar
const isOpen = ref(false);
const toggleOpen = () => {
    isOpen.value = !isOpen.value;
};

// Setup form with Inertia
const form = useForm({
    content: "",
    commentable_id: props.commentable_id,
    commentable_type: props.commentable_type,
    parent_comment_id: props.parent_comment_id ?? null
});

// Submit form handler
const submit = () => {
    console.log("Content: " + form.content);
    form.post(
        route("comments.store"),
        {
            preserveScroll: true,
            onFailure: () => {
                console.warn("Failed to post comment: " + form);
            },
            onSuccess: () => {
                form.reset("content");
                console.log("Successful comment post!");
                isOpen.value = false;
            },
        }
    );
};
</script>

<template>
    <div class="mt-4">
        <!-- Iconify comment button to toggle the comment bar -->
        <button
            @click="toggleOpen"
            class="flex items-center space-x-2 focus:outline-none"
        >
            <Icon
                icon="mdi:comment-outline"
                class="w-6 h-6 text-gray-600 hover:text-gray-800"
            />
            <span class="text-sm text-gray-600">Comment</span>
        </button>
        
        <!-- Comment bar shown when isOpen is true -->
        <div v-if="isOpen" class="mt-4">
            <form @submit.prevent="submit" class="space-y-4">
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
    </div>
</template>
