<script setup>
import { Icon } from "@iconify/vue";
import Tag from "@/Components/Tag.vue";

const props = defineProps({
    tags: {
        type: Array,
        required: true,
    },
    label: {
        type: String,
        default: null,
    },
    labelIcon: {
        type: String,
        default: null,
    },
    tagIcon: {
        type: String,
        default: null,
    },
    tagIconSize: {
        type: [String, Number],
        default: 10,
    },
    variant: {
        type: String,
        default: 'default',
    },
    maxVisible: {
        type: Number,
        default: null,
    },
});

const visibleTags = props.maxVisible ? props.tags.slice(0, props.maxVisible) : props.tags;
</script>

<template>
    <div v-if="tags.length > 0" class="flex items-center gap-1.5 flex-wrap">
        <Icon
            v-if="labelIcon"
            :icon="labelIcon"
            width="12"
            height="12"
            class="text-gray-500 dark:text-gray-100"
        />
        <span
            v-if="label"
            class="text-xs font-semibold text-gray-600 dark:text-gray-100"
        >
            {{ label }}
        </span>
        <Tag
            v-for="tag in visibleTags"
            :key="tag"
            :tag="tag"
            :variant="variant"
            :icon="tagIcon"
            :icon-size="tagIconSize"
        />
    </div>
</template>
