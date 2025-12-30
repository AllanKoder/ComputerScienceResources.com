<script setup>
import { Icon } from "@iconify/vue";
import { computed } from "vue";

const props = defineProps({
    tag: {
        type: String,
        required: true,
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'selected', 'highlighted'].includes(value),
    },
    icon: {
        type: String,
        default: null,
    },
    iconSize: {
        type: [String, Number],
        default: 10,
    },
    removable: {
        type: Boolean,
        default: false,
    },
    count: {
        type: [Number, String],
        default: null,
    },
});

const emit = defineEmits(['remove']);

const isEverything = computed(() => props.tag === 'everything');

const tagClasses = computed(() => {
    const base = 'inline-flex items-center rounded-full text-xs font-medium transition-colors';

    if (isEverything.value) {
        // "everything" tag always gets bold, border-2, and orange colors
        if (props.variant === 'highlighted') {
            return `${base} px-2.5 py-0.5 font-bold bg-orange-100 text-orange-700 border-2 border-orange-500 dark:bg-orange-900/30 dark:text-orange-300 dark:border-orange-500`;
        } else if (props.variant === 'selected') {
            return `${base} px-3 py-1 font-bold bg-orange-100 text-orange-700 border-2 border-orange-300 dark:bg-orange-900/30 dark:text-orange-300 dark:border-orange-700`;
        } else {
            return `${base} px-2 py-0.5 font-bold bg-orange-50 text-orange-700 border-2 border-orange-200 dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-600`;
        }
    }

    // Regular tags
    if (props.variant === 'selected') {
        return `${base} px-3 py-1 bg-secondary text-primaryDark dark:bg-gray-700 dark:text-white`;
    } else if (props.variant === 'highlighted') {
        return `${base} px-2 py-0.5 bg-secondary text-primaryDark dark:bg-gray-800 dark:text-primaryLight`;
    } else {
        return `${base} px-2 py-0.5 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-100 bg-transparent`;
    }
});

const iconClasses = computed(() => {
    if (isEverything.value) {
        return 'text-orange-700 dark:text-orange-300';
    }
    return props.variant === 'selected' ? 'text-primaryDark dark:text-white' : '';
});

const countClasses = computed(() => {
    if (isEverything.value) {
        // Orange theme for "everything" tag count
        if (props.variant === 'highlighted' || props.variant === 'selected') {
            return 'bg-orange-200 text-orange-800 dark:bg-orange-800 dark:text-orange-200';
        } else {
            return 'bg-orange-200 text-orange-700 dark:bg-orange-800 dark:text-orange-300';
        }
    }
    
    // Default gray theme for regular tags
    if (props.variant === 'highlighted') {
        return 'bg-secondary text-primaryDark dark:bg-gray-800 dark:text-primaryLight';
    } else if (props.variant === 'selected') {
        return 'bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300';
    } else {
        return 'bg-gray-100 text-gray-600 dark:bg-gray-900 dark:text-gray-300';
    }
});
</script>

<template>
    <span :class="tagClasses">
        <button
            v-if="removable"
            @click="emit('remove', tag)"
            class="mr-2"
            :class="iconClasses"
            type="button"
        >
            <Icon icon="mdi:close" />
        </button>
        <Icon
            v-if="icon"
            :icon="icon"
            :width="iconSize"
            :height="iconSize"
            class="mr-1"
            :class="iconClasses"
        />
        <span>{{ tag }}</span>
        <span
            v-if="count !== null"
            class="ml-1.5 text-xs px-2 py-1 rounded-full"
            :class="countClasses"
        >
            {{ count }}
        </span>
    </span>
</template>
