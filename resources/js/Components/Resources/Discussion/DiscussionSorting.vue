<script setup>
import { router } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import SortByDropdown from "@/Components/Comments/SortByDropdown.vue";

const props = defineProps({
    resourceId: {
        type: Number,
        required: true
    },
    initialValue: {
        type: String,
        required: true
    }
})

function handleSortChange(newSortType) {
    if (props.initialValue == newSortType) return;
    // Change the sort_by parameter
    const baseUrl = route('resources.show', {
        computerScienceResource: props.resourceId,
        tab: 'discussion',
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
    ></SortByDropdown>
</template>