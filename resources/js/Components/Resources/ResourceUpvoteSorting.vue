<script setup>
import { router } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import SortByDropdown from "@/Components/Comments/SortUpvotesByDropdown.vue";

const props = defineProps({
    resourceId: {
        type: Number,
        required: true
    },
    initialValue: {
        type: String,
        default: 'top',
    },
    tab: {
        type: String,
        default: 'discussion',
    }
})

function handleSortChange(newSortType) {
    // Change the sort_by parameter
    const baseUrl = route('resources.show', {
        computerScienceResource: props.resourceId,
        tab: props.tab,
    });

    // Create a new URL object based on the current location
    const url = new URL(baseUrl, window.location.origin);

    // Append the query parameter
    url.searchParams.set('sort_by', newSortType);

    // Visit the new URL with Inertia
    router.visit(url.toString(), {
        preserveState: true,
        preserveScroll: true
    });
}
</script>

<template>
    <SortByDropdown
        @change="handleSortChange"
        :initial-value="initialValue"
    ></SortByDropdown>
</template>