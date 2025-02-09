<script setup>
import { defineProps, ref, watch } from "vue";
import { Icon } from "@iconify/vue";
import { Link, router } from "@inertiajs/vue3";

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

watch(
    () => props.votes,
    (newVotes) => {
        votes.value = newVotes;
    }
);

</script>

<template>
    <div class="flex flex-col items-center">
        <Link
            :href="route('upvote', { id: props.resourceId, type: 'resource' })"
            method="post"
            as="button"
            preserve-scroll
        >
            <Icon icon="mdi:chevron-up" width="24" height="24" />
        </Link>
        <span class="text-lg font-bold">{{ votes }}</span>
        <Link
            :href="route('downvote', { id: props.resourceId, type: 'resource' })"
            method="post"
            as="button"
            preserve-scroll
        >
            <Icon icon="mdi:chevron-down" width="24" height="24" />
        </Link>
    </div>
</template>

