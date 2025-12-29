<script setup>
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";
import FieldDisplay from "./FieldDisplay.vue";

defineProps({
    changedFields: {
        type: Array,
        required: true,
    },
    originalResource: {
        type: Object,
        required: true,
    },
    hasChanges: {
        type: Boolean,
        required: true,
    },
});
</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Proposed Changes Column -->
        <div
            class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg"
        >
            <div
                class="bg-primary/5 dark:bg-primary/10 border-b border-gray-200 dark:border-gray-800 px-4 py-3"
            >
                <h2
                    class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2"
                >
                    <Icon
                        icon="mdi:plus-circle"
                        class="w-5 h-5 text-primary"
                    />
                    Proposed Changes
                </h2>
            </div>
            <div class="p-4 space-y-4">
                <FieldDisplay
                    v-for="field in changedFields"
                    :key="field.key"
                    :field="field"
                    :value="field.proposedValue"
                    altText="Proposed Image"
                />
                <p
                    v-if="!hasChanges"
                    class="text-gray-500 dark:text-gray-400 italic"
                >
                    No changes were proposed.
                </p>
            </div>
        </div>

        <!-- Current Version Column -->
        <div
            class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg"
        >
            <div
                class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800 px-4 py-3"
            >
                <Link
                    :href="
                        route('resources.show', {
                            slug: originalResource.slug,
                        })
                    "
                    class="group"
                >
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2 group-hover:text-primary duration-200"
                    >
                        <Icon
                            icon="mdi:file-document"
                            class="w-5 h-5 text-gray-600 dark:text-gray-300 group-hover:text-primary duration-200"
                        />
                        Current Version
                    </h2>
                </Link>
            </div>
            <div class="p-4 space-y-4">
                <FieldDisplay
                    v-for="field in changedFields"
                    :key="field.key"
                    :field="field"
                    :value="field.originalValue"
                    altText="Current Image"
                />
                <p
                    v-if="!hasChanges"
                    class="text-gray-500 dark:text-gray-400 italic"
                >
                    No changes to compare.
                </p>
            </div>
        </div>
    </div>
</template>
