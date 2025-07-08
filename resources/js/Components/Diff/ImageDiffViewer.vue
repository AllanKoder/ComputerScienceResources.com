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
        <ImageCompare class="sm:!w-60 sm:!h-60" v-if="originalImage && proposedImage">
            <template #left>
                <ResourceThumbnail :src="originalImage" alt="Original Image" />
            </template>
            <template #right>
                <ResourceThumbnail :src="proposedImage" alt="Proposed Image" />
            </template>
        </ImageCompare>
        <div v-if="proposedImage"
        class="text-sm text-gray-500 italic text-center">
            New Image Added
            <ResourceThumbnail :src="proposedImage" alt="Proposed Image" />
        </div>
        <div v-if="originalImage"
            class="text-sm text-gray-500 italic text-center">
            Image Removed
            <ResourceThumbnail :src="null" alt="Proposed Image" />
        </div>
    </div>
</template>
