<script setup>
import { ref, computed } from 'vue';
import { Icon } from '@iconify/vue';
import CreateResourceReview from '@/Components/Resources/Reviews/CreateResourceReview.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    userReview: {
        type: Object,
        default: null,
    },
    resourceId: {
        type: Number,
        required: true,
    },
    resourceSlug: {
        type: String,
        required: true,
    },
});

const isEditingMode = computed(() => props.userReview !== null);

const showForm = ref(false);

const buttonText = computed(() => {
    if (showForm.value) {
        return 'Cancel';
    }
    return isEditingMode.value ? 'Edit Your Review' : 'Write a Review';
});

const buttonIcon = computed(() => {
    if (showForm.value) {
        return 'mdi:close';
    }
    return isEditingMode.value ? 'mdi:pencil' : 'mdi:plus';
});
</script>

<template>
    <div class="px-6 max-w-7xl mx-auto">
        <!-- Toggle Button -->
        <div class="flex justify-end mb-4">
            <PrimaryButton
                @click="showForm = !showForm"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
            >
                <Icon :icon="buttonIcon" class="text-xl" />
                {{ buttonText }}
            </PrimaryButton>
        </div>

        <!-- Create a review -->
        <div v-show="showForm" class="mb-8">
            <CreateResourceReview
                :resource-id="props.resourceId"
                :resource-slug="props.resourceSlug"
                :resource-review="props.userReview"
                :is-editing-mode="isEditingMode"
            />
        </div>
    </div>
</template>
