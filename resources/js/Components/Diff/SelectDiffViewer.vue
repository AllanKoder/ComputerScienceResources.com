<script setup>
import { computed } from "vue";

const props = defineProps({
    field: {
        type: Object,
        required: true,
    },
});

const formattedOriginal = computed(() => {
    const value = props.field.originalValue;
    if (props.field.formatter) {
        return props.field.formatter(value);
    }
    return value || "Not set";
});

const formattedProposed = computed(() => {
    const value = props.field.proposedValue;
    if (props.field.formatter) {
        return props.field.formatter(value);
    }
    return value || "Not set";
});
</script>

<template>
    <div class="font-mono text-sm space-y-1">
        <div
            class="bg-red-100/50 text-red-800 px-3 py-1 rounded-md w-full"
        >
            <span class="font-bold mr-2">-</span>
            <del>{{ formattedOriginal }}</del>
        </div>
        <div
            class="bg-green-100/50 text-green-800 px-3 py-1 rounded-md w-full"
        >
            <span class="font-bold mr-2">+</span>
            <span>{{ formattedProposed }}</span>
        </div>
    </div>
</template>
