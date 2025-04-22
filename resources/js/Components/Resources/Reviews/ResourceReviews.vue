<script setup>
import { defineProps, ref } from "vue";
import { Icon } from '@iconify/vue';
import CreateResourceReview from "@/Components/Resources/Reviews/CreateResourceReview.vue";
import ResourceReview from "./ResourceReview.vue";

const props = defineProps({
    reviews: {
        type: Array,
        required: true,
    },
    resourceId: {
        type: Number,
        required: true,
    },
});

const showForm = ref(false);
</script>

<template>
    <div class="p-6 max-w-4xl mx-auto">
        <!-- Toggle Button -->
        <div class="flex justify-end mb-4">
            <button
                @click="showForm = !showForm"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
            >
                <Icon :icon="showForm ? 'mdi:eye-off' : 'mdi:eye'" class="text-xl" />
                {{ showForm ? 'Hide' : 'Write a Review' }}
            </button>
        </div>

        <!-- Create a review -->
        <div v-if="showForm" class="mb-8">
            <CreateResourceReview :resource-id="props.resourceId" />
        </div>

        <!-- Review List -->
        <div class="space-y-6">
            <div
                v-for="review in reviews"
                :key="review.id"
                class="bg-white/70 backdrop-blur-md p-6 rounded-lg shadow-md"
            >
                <ResourceReview :review="review" />
            </div>
        </div>
    </div>
</template>
