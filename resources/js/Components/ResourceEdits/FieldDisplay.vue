<script setup>
import Tag from "primevue/tag";
import ResourceThumbnail from "@/Components/Resources/ResourceThumbnail.vue";

defineProps({
    field: {
        type: Object,
        required: true,
    },
    value: {
        required: true,
    },
    altText: {
        type: String,
        default: 'Image',
    },
});
</script>

<template>
    <div>
        <h3 class="font-semibold text-gray-600 dark:text-gray-300 mb-2">
            {{ field.label }}:
        </h3>
        <div v-if="field.key === 'image_url'">
            <p v-if="!value" class="italic">Image Removed</p>
            <ResourceThumbnail
                v-else
                :src="value"
                :alt="altText"
            />
        </div>
        <div
            v-else-if="Array.isArray(value)"
            class="flex flex-wrap gap-2"
        >
            <Tag
                v-for="item in value"
                :key="item"
                :value="field.formatter ? field.formatter(item) : item"
            />
        </div>
        <div
            v-else
            class="text-gray-800 dark:text-gray-100 p-2 rounded"
        >
            {{
                field.formatter
                    ? field.formatter(value)
                    : value
            }}
        </div>
    </div>
</template>
