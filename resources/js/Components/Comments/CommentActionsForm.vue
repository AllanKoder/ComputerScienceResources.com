<script setup>
import { ref, reactive, inject } from "vue";
import { Icon } from "@iconify/vue";
import axios from "axios";

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

const commentableType = inject('commentableType');
const commentableId = inject('commentableId');

const toggleOpen = () => {
  isOpen.value = !isOpen.value;
};

const form = reactive({
  content: "",
  commentable_id: commentableId,
  commentable_type: commentableType,
  parent_comment_id: props.parentCommentId ?? null,
  errors: {},
  processing: false,
});

const submit = () => {
  form.processing = true;
  form.errors = {};

  axios.post(route("comments.store"), form)
    .then(() => {
      form.content = "";
      console.log("Successful comment post!");
      isOpen.value = false;
    })
    .catch((error) => {
      if (error.response && error.response.data && error.response.data.errors) {
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
      <button @click="toggleOpen" class="text-sm text-blue-500 hover:underline">
        <Icon icon="mdi:comment-outline" class="w-5 h-5 inline-block" /> {{ label }}
      </button>
    </div>

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
