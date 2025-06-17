<script setup>
import { defineProps, ref, watch } from "vue";
import axios from "axios";
import { Icon } from "@iconify/vue";
import { router } from '@inertiajs/vue3'

const props = defineProps({
    upvotableKey: {
        type: String,
        required: true,
    },
    upvotableId: {
        type: Number,
        required: true,
    },
    initialVotes: {
        type: Number,
        required: true,
    },
    userVote: {
        type: Number,
        required: true,
    },
    flexRow: {
        type: Boolean,
        default: false
    },
    refresh: {
        type: Boolean,
        default: false
    }
});

const votes = ref(props.initialVotes);
const upvoteLoading = ref(false);
const downvoteLoading = ref(false);

const userVote = ref(props.userVote);

// If the parent ever changes the initial votes, keep in sync.
watch(
    () => props.initialVotes,
    (newVotes) => {
        votes.value = newVotes;
    }
);

async function handleUpvote() {
    if (upvoteLoading.value) return;
    upvoteLoading.value = true;
    try {
        const response = await axios.post(
            route("upvote", {
                id: props.upvotableId,
                typeKey: props.upvotableKey,
            })
        );
        userVote.value = response.data.userVote;
        votes.value += response.data.changeFromVote;

        if (props.refresh)
        {
            router.reload({preserveScroll: true});
        }

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
            route("downvote", {
                id: props.upvotableId,
                typeKey: props.upvotableKey,
            })
        );
        userVote.value = response.data.userVote;
        votes.value += response.data.changeFromVote;

        if (props.refresh)
        {
            router.reload({preserveScroll: true});
        }
    } catch (error) {
        console.error("Error downvoting:", error);
    } finally {
        downvoteLoading.value = false;
    }
}
</script>

<template>
    <div class="flex items-center"
        :class="{ 'flex-row-reverse': flexRow, 'flex-col': !flexRow }">
        <!-- Upvote Section -->
        <button
            @click="handleUpvote"
            :disabled="upvoteLoading"
            :class="{
                'opacity-50': upvoteLoading,
            }"
            class="cursor-pointer"
        >
            <!-- Already Upvoted Icon -->
            <slot v-if="userVote > 0" name="alreadyUpvotedIcon">
                <span class="text-red-500">
                    <!-- Default to original upvote icon with active state -->
                    <Icon icon="mdi:chevron-up" width="24" height="24" />
                </span>
            </slot>

            <!-- Regular Upvote Icon -->
            <slot v-else name="upvoteIcon">
                <Icon icon="mdi:chevron-up" width="24" height="24" />
            </slot>
        </button>

        <!-- Vote Count -->
        <slot :votes="votes" name="votes">
            <span class="text-lg font-bold">{{ votes }}</span>
        </slot>

        <!-- Downvote Section -->
        <button
            @click="handleDownvote"
            :disabled="downvoteLoading"
            :class="{
                'opacity-50': downvoteLoading,
            }"
            class="cursor-pointer"
        >
            <!-- Already Downvoted Icon -->
            <slot v-if="userVote < 0" name="alreadyDownvotedIcon">
                <span class="text-blue-500">
                    <!-- Default to original downvote icon with active state -->
                    <Icon icon="mdi:chevron-down" width="24" height="24" />
                </span>
            </slot>

            <!-- Regular Downvote Icon -->
            <slot v-else name="downvoteIcon">
                <Icon icon="mdi:chevron-down" width="24" height="24" />
            </slot>
        </button>
    </div>
</template>
