<script setup>
import { computed } from "vue";

const props = defineProps({
    field: {
        type: Object,
        required: true,
    },
});

const originalTags = computed(() => props.field.originalValue || []);
const proposedTags = computed(() => props.field.proposedValue || []);

const removedTags = computed(() =>
    originalTags.value.filter(
        (tag) =>
            tag &&
            !proposedTags.value.some(
                (pTag) => pTag && pTag.toLowerCase() === tag.toLowerCase()
            )
    )
);

const addedTags = computed(() =>
    proposedTags.value.filter(
        (tag) =>
            tag &&
            !originalTags.value.some(
                (oTag) => oTag && oTag.toLowerCase() === tag.toLowerCase()
            )
    )
);

const formatValue = (value) => {
    return props.field.formatter ? props.field.formatter(value) : value;
};
</script>

<template>
    <div class="font-mono text-sm space-y-1">
        <div v-if="removedTags.length > 0">
            <div
                v-for="tag in removedTags"
                :key="tag"
                class="bg-red-100/50 text-red-800 px-3 py-1 rounded-md w-full inline-flex items-center mb-1"
            >
                <span class="font-bold mr-2">-</span>
                <del>{{ formatValue(tag) }}</del>
            </div>
        </div>
        <div v-if="addedTags.length > 0">
            <div
                v-for="tag in addedTags"
                :key="tag"
                class="bg-green-100/50 text-green-800 px-3 py-1 rounded-md w-full inline-flex items-center"
            >
                <span class="font-bold mr-2">+</span>
                <span>{{ formatValue(tag) }}</span>
            </div>
        </div>
    </div>
</template>
