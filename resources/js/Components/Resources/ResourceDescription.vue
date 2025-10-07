<script setup>
import { defineProps, computed } from "vue";
import { Icon } from "@iconify/vue";
import StarRating from "@/Components/StarRating/StarRating.vue";
import { platformIcons, pricingIcons, difficultyIcons } from "@/Helpers/icons";
import {
    platformLabels,
    pricingLabels,
    difficultyLabels,
} from "@/Helpers/labels";

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="flex-grow w-full">
        <div
            class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-4 gap-4"
        >
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
                    </div>
                    <div
                        class="inline-flex items-center gap-1 text-lg font-medium text-gray-700 dark:text-gray-300"
                    >
                        <Icon icon="mdi:account-group" width="20" height="20" />
                        {{ props.resource.review_summary?.review_count || 0 }}
                    </div>
                </div>
            </div>
        </div>

        <p
            class="text-gray-700 dark:text-gray-200 mb-6 text-base leading-relaxed whitespace-pre-line"
        >
            {{ props.resource.description }}
        </p>

        <!-- Resource Metadata Section -->
        <div class="flex flex-col gap-3 mb-4">
            <!-- Row 1: Resource Properties -->
            <div class="flex flex-wrap items-center gap-4 text-sm">
                <!-- Difficulties -->
                <div
                    class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-900/70 px-3 py-1 rounded-md"
                >
                    <span class="font-semibold text-gray-900 dark:text-gray-100"
                        >Difficulties:</span
                    >
                    <div
                        class="inline-flex flex-wrap items-center gap-2 text-gray-700 dark:text-gray-200"
                    >
                        <span
                            v-for="level in props.resource.difficulties"
                            :key="level"
                            class="inline-flex items-center gap-1"
                        >
                            <Icon
                                :icon="difficultyIcons[level.trim()]"
                                width="16"
                                height="16"
                            />
                            {{ difficultyLabels[level.trim()] }}
                        </span>
                    </div>
                </div>

                <!-- Pricing -->
                <div
                    class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-900/70 px-3 py-1 rounded-md"
                >
                    <span class="font-semibold text-gray-900 dark:text-gray-100"
                        >Pricing:</span
                    >
                    <span
                        class="inline-flex items-center gap-1 text-gray-700 dark:text-gray-200"
                    >
                        <Icon
                            :icon="pricingIcons[props.resource.pricing]"
                            width="16"
                            height="16"
                        />
                        {{ pricingLabels[props.resource.pricing] }}
                    </span>
                </div>

                <!-- Platforms -->
                <div
                    class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-900/70 px-3 py-1 rounded-md"
                >
                    <span class="font-semibold text-gray-900 dark:text-gray-100"
                        >Platforms:</span
                    >
                    <div
                        class="inline-flex flex-wrap items-center gap-2 text-gray-700 dark:text-gray-200"
                    >
                        <span
                            v-for="type in props.resource.platforms"
                            :key="type"
                            class="inline-flex items-center gap-1"
                        >
                            <Icon
                                :icon="platformIcons[type.trim()]"
                                width="16"
                                height="16"
                            />
                            {{ platformLabels[type.trim()] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Row 2: Tags -->
            <div class="flex flex-wrap items-center gap-4 text-sm">
                <!-- Topic Tags -->
                <div
                    v-if="props.resource.topics_tags?.length"
                    class="flex items-center gap-1 px-2 py-1 rounded-md"
                >
                    <span
                        class="inline-flex items-center gap-1 font-semibold text-blue-700 dark:text-blue-200"
                    >
                        <Icon icon="mdi:bookmark" width="14" height="14" />
                        Topics:
                    </span>
                    <div class="inline-flex flex-wrap items-center gap-0.5">
                        <span
                            v-for="tag in props.resource.topics_tags"
                            :key="tag"
                            class="inline-flex items-center gap-1 bg-blue-100/50 dark:bg-blue-900/60 px-2 py-0.5 rounded-full text-blue-700 dark:text-blue-100"
                        >
                            {{ tag }}
                        </span>
                    </div>
                </div>

                <!-- Programming Language Tags -->
                <div
                    v-if="props.resource.programming_languages_tags?.length"
                    class="flex items-center gap-1.5 px-2 py-1 rounded-md"
                >
                    <span
                        class="inline-flex items-center gap-1 font-semibold text-purple-700 dark:text-purple-200"
                    >
                        <Icon
                            icon="mdi:language-typescript"
                            width="14"
                            height="14"
                        />
                        Languages:
                    </span>
                    <div class="inline-flex flex-wrap items-center gap-0.5">
                        <span
                            v-for="tag in props.resource
                                .programming_languages_tags"
                            :key="tag"
                            class="inline-flex items-center gap-1 bg-purple-100/50 dark:bg-purple-900/60 px-2 py-0.5 rounded-full text-purple-700 dark:text-purple-100"
                        >
                            {{ tag }}
                        </span>
                    </div>
                </div>

                <!-- General Tags -->
                <div
                    v-if="props.resource.general_tags?.length"
                    class="flex items-center gap-1.5 px-2 py-1 rounded-md"
                >
                    <span
                        class="inline-flex items-center gap-1 font-semibold text-yellow-700 dark:text-yellow-200"
                    >
                        <Icon icon="mdi:tag" width="14" height="14" />
                        Tags:
                    </span>
                    <div class="inline-flex flex-wrap items-center gap-0.5">
                        <span
                            v-for="tag in props.resource.general_tags"
                            :key="tag"
                            class="inline-flex items-center gap-1 bg-yellow-100/50 dark:bg-yellow-900/60 px-2 py-0.5 rounded-full text-yellow-700 dark:text-yellow-100"
                        >
                            {{ tag }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
