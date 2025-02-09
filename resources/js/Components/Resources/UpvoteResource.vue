<script setup>
import { defineProps, ref, watch } from "vue";
import axios from "axios";
import { Icon } from "@iconify/vue";

const props = defineProps({
    resourceId: {
        type: Number,
        required: true,
    },
    votes: {
        type: Number,
        required: true,
    },
});

const votes = ref(props.votes);
const upvoteLoading = ref(false);
const downvoteLoading = ref(false);

// If the parent ever changes the initial votes, keep in sync.
watch(
    () => props.votes,
    (newVotes) => {
        votes.value = newVotes;
    }
);

async function handleUpvote() {
    if (upvoteLoading.value) return;
    upvoteLoading.value = true;
    try {
        const response = await axios.post(
            route('upvote', { id: props.resourceId, type: 'resource' })
        );
        votes.value = response.data.votes;
    } catch (error) {
        console.error("Error upvoting:", error);
    } finally {
        upvoteLoading.value = false;
    }
}

async function handleDownvote() {
    if (downvoteLoading.value) return;
    downvoteLoading.value = true;
    try {
        const response = await axios.post(
            route('downvote', { id: props.resourceId, type: 'resource' })
        );
        votes.value = response.data.votes;
    } catch (error) {
        console.error("Error downvoting:", error);
    } finally {
        downvoteLoading.value = false;
    }
}
</script>

<template>
    <div class="flex flex-col items-center">
        <button 
            @click="handleUpvote" 
            :disabled="upvoteLoading" 
            :class="{'opacity-50': upvoteLoading}" 
            class="cursor-pointer"
        >
            <Icon icon="mdi:chevron-up" width="24" height="24" />
        </button>
        <span class="text-lg font-bold">{{ votes }}</span>
        <button 
            @click="handleDownvote" 
            :disabled="downvoteLoading" 
            :class="{'opacity-50': downvoteLoading}" 
            class="cursor-pointer"
        >
            <Icon icon="mdi:chevron-down" width="24" height="24" />
        </button>
    </div>
</template>
