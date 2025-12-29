<script setup>
import { Icon } from "@iconify/vue";

defineProps({
    changedFields: {
        type: Array,
        required: true,
    },
    hasChanges: {
        type: Boolean,
        required: true,
    },
});
</script>

<template>
    <div v-if="hasChanges" class="space-y-6">
        <div
            v-for="field in changedFields"
            :key="field.key"
            class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden"
        >
            <div
                class="bg-gray-100 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-800"
            >
                <h3
                    class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2"
                >
                    <Icon
                        icon="mdi:file-edit"
                        class="w-4 h-4 text-primary"
                    />
                    {{ field.label }}
                </h3>
            </div>
            <div class="p-4">
                <component
                    :is="field.component"
                    :field="field"
                />
            </div>
        </div>
    </div>
    <p v-else class="text-gray-500 dark:text-gray-400 italic p-4">
        No changes to display in diff.
    </p>
</template>
