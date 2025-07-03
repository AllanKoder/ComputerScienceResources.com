<script setup>
import ImageCompare from 'primevue/imagecompare';
import ResourceThumbnail from "@/Components/Resources/ResourceThumbnail.vue";

const props = defineProps({
    field: {
        type: Object,
        required: true,
    },
});

const originalImage = props.field.originalValue;
const proposedImage = props.field.proposedValue;
</script>

<template>
    <div class="flex justify-center">
        <ImageCompare class="sm:!w-96 sm:!h-96" v-if="originalImage && proposedImage">
            <template #left>
                <ResourceThumbnail :src="originalImage" alt="Original Image" />
            </template>
            <template #right>
                <ResourceThumbnail :src="proposedImage" alt="Proposed Image" />
            </template>
        </ImageCompare>
        <div v-else class="text-sm text-gray-500 italic">
            No image comparison available.
        </div>
    </div>
</template>
