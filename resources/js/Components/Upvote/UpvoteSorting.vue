<script setup>
import { router } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import SortUpvotesByDropdown from "@/Components/Comments/SortUpvotesByDropdown.vue";

const props = defineProps({
    routeName: {
        type: String,
        required: true
    },
    routeParams: {
        type: Object,
        default: () => ({})
    },
    initialValue: {
        type: String,
        default: 'top',
    }
})

function handleSortChange(newSortType) {
    // Change the sort_by parameter
    const baseUrl = route(props.routeName, props.routeParams);

    // Create a new URL object based on the current location
    const url = new URL(baseUrl, window.location.origin);

    // Append the query parameter
    url.searchParams.set('sort_by', newSortType);

    // Visit the new URL with Inertia
    router.visit(url.toString(), {
        preserveState: true,
        preserveScroll: true,
        viewTransition: true
    });
}
</script>

<template>
    <SortUpvotesByDropdown
        @change="handleSortChange"
        :initial-value="initialValue"
    ></SortUpvotesByDropdown>
</template>
