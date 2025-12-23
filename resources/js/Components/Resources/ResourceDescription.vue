<script setup>
import { defineProps } from "vue";
import { Icon } from "@iconify/vue";
import StarRating from "@/Components/StarRating/StarRating.vue";

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="flex-grow w-full">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-4 gap-4">
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ props.resource.name }}
                </h1>
                <a
                    :href="props.resource.page_url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-gray-400 hover:text-primary dark:hover:text-primaryLight transition-colors duration-200"
                >
                    <Icon icon="mdi:external-link" width="24" height="24" />
                </a>
            </div>
            <div class="flex flex-col gap-4">
                <!-- Overall Rating with Review Count -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <StarRating
                            :model-value="
                                Number(
                                    props.resource.review_summary
                                        ?.overall_rating
                                )
                            "
                            :size="23"
                        />
                        <span class="text-lg font-medium text-gray-700 dark:text-gray-300">
                            {{ Number(props.resource.review_summary?.overall_rating || 0).toFixed(1) }}
                        </span>
                    </div>
                    <div class="inline-flex items-center gap-1 text-lg font-medium text-gray-700 dark:text-gray-300">
                        <Icon icon="mdi:account-group" width="20" height="20" />
                        {{ props.resource.review_summary?.review_count || 0 }} reviews
                    </div>
                </div>
            </div>
        </div>

        <p class="text-gray-700 dark:text-gray-200 mb-6 text-base leading-relaxed whitespace-pre-line">
            {{ props.resource.description }}
        </p>
        <!-- Platforms, Languages, Tags (bottom, horizontal list) -->
        <div class="flex flex-col gap-2 mt-6">
            <!-- Platforms -->
            <div v-if="props.resource.platforms?.length" class="flex items-center gap-2 flex-wrap">
                <span v-for="platform in props.resource.platforms" :key="platform" class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                    {{ platformLabels[platform] }}
                </span>
            </div>
            <!-- Languages -->
            <div v-if="props.resource.programming_languages_tags?.length" class="flex items-center gap-2 flex-wrap">
                <span v-for="language in props.resource.programming_languages_tags" :key="language" class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                    {{ language }}
                </span>
            </div>
            <!-- Tags -->
            <div v-if="props.resource.general_tags?.length" class="flex items-center gap-2 flex-wrap">
                <span v-for="tag in props.resource.general_tags" :key="tag" class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                    {{ tag }}
                </span>
            </div>
        </div>
    </div>
</template>
