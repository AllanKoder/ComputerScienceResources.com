<script setup>
import { Icon } from "@iconify/vue";
import Tag from "@/Components/Tag.vue";
import { computed } from "vue";

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

const sortedTags = computed(() => {
    const tagsCopy = [...props.tags];
    return tagsCopy.sort((a, b) => {
        const aLower = a.toLowerCase();
        const bLower = b.toLowerCase();

        if (aLower === 'everything') return -1;
        if (bLower === 'everything') return 1;
        return 0;
    });
});

const visibleTags = computed(() =>
    props.maxVisible ? sortedTags.value.slice(0, props.maxVisible) : sortedTags.value
);
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
        <span
            v-if="maxVisible && sortedTags.length > maxVisible"
            class="text-xs text-gray-500 dark:text-gray-400"
        >
            ...
        </span>
    </div>
</template>
