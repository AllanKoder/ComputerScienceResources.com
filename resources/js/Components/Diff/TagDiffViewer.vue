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
            !proposedTags.value.some(
                (pTag) => pTag.name.toLowerCase() === tag.name.toLowerCase()
            )
    )
);

const addedTags = computed(() =>
    proposedTags.value.filter(
        (tag) =>
            !originalTags.value.some(
                (oTag) => oTag.name.toLowerCase() === tag.name.toLowerCase()
            )
    )
);
</script>

<template>
    <div class="font-mono text-sm space-y-1">
        <div v-if="removedTags.length > 0">
            <div
                v-for="tag in removedTags"
                :key="tag.id"
                class="bg-red-100/50 text-red-800 px-3 py-1 rounded-md w-full inline-flex items-center mb-1"
            >
                <span class="font-bold mr-2">-</span>
                <del>{{ tag.name }}</del>
            </div>
        </div>
        <div v-if="addedTags.length > 0">
            <div
                v-for="tag in addedTags"
                :key="tag.id"
                class="bg-green-100/50 text-green-800 px-3 py-1 rounded-md w-full inline-flex items-center"
            >
                <span class="font-bold mr-2">+</span>
                <span>{{ tag.name }}</span>
            </div>
        </div>
    </div>
</template>
