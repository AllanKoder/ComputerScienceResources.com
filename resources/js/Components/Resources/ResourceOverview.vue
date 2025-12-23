<script setup>
import { Icon } from "@iconify/vue";
import UpvoteResource from "@/Components/Upvote/Upvotable.vue";
import ResourceThumbnail from "@/Components/Resources/ResourceThumbnail.vue";
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
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-sm shadow-sm px-4 py-4">
        <div class="flex items-start gap-4">
            <!-- Image Section -->
            <div class="shrink-0 flex flex-row my-auto gap-3">
                <!-- Vote Section -->
                <UpvoteResource
                    :upvotable-id="props.resource.id"
                    :upvotable-key="'resource'"
                    :initial-votes="props.resource.vote_score"
                    :user-vote="props.resource.user_vote"
                    class="flex flex-col my-auto pt-1"
                />
                <ResourceThumbnail
                    :src="props.resource.image_url"
                    :alt="props.resource.name"
                />
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <!-- Primary info row -->
                <div class="flex items-start gap-3 mb-3 flex-wrap">
                    <Icon icon="mdi:star" width="14" height="14" class="text-primary mt-1" />

                    <!-- Topics -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Topics:</span>
                        <div class="flex items-center gap-1.5">
                            <span
                                v-for="topic in props.resource.topics_tags"
                                :key="topic"
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-transparent"
                            >
                                <Icon icon="mdi:lightbulb-outline" width="12" height="12" class="mr-1" />
                                {{ topic }}
                            </span>
                        </div>
                    </div>

                    <!-- Difficulty -->
                    <div v-if="props.resource.difficulties?.length" class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Difficulty:</span>
                        <div class="flex items-center gap-1.5">
                            <span
                                v-for="difficulty in props.resource.difficulties"
                                :key="difficulty"
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-transparent text-primaryDark border border-primary/20"
                            >
                                <Icon :icon="difficultyIcons[difficulty]" width="12" height="12" class="mr-1" />
                                {{ difficultyLabels[difficulty] }}
                            </span>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Pricing:</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-transparent text-primaryDark border border-primary/20">
                            <Icon :icon="pricingIcons[props.resource.pricing]" width="12" height="12" class="mr-1" />
                            {{ pricingLabels[props.resource.pricing] }}
                        </span>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-2xl font-bold mb-2 text-gray-900 dark:text-gray-100 text-balance">
                    {{ props.resource.name }}
                </h1>

                <!-- Description -->
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 leading-relaxed whitespace-pre-line">
                    {{ props.resource.description }}
                </p>

                <!-- Secondary info row -->
                <div class="flex items-left gap-2 flex-col mb-3">
                    <!-- Platforms -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Platforms:</span>
                        <span
                            v-for="platform in props.resource.platforms"
                            :key="platform"
                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-transparent"
                        >
                            <Icon :icon="platformIcons[platform]" width="10" height="10" class="mr-1" />
                            {{ platformLabels[platform] }}
                        </span>
                    </div>
                    <!-- Languages -->
                    <div v-if="props.resource.programming_languages_tags?.length" class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Languages:</span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span
                                v-for="language in props.resource.programming_languages_tags"
                                :key="language"
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-transparent"
                            >
                                <Icon icon="mdi:code-tags" width="10" height="10" class="mr-1" />
                                {{ language }}
                            </span>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div v-if="props.resource.general_tags?.length" class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Tags:</span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span
                                v-for="tag in props.resource.general_tags"
                                :key="tag"
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-transparent"
                            >
                                <Icon icon="mdi:label-outline" width="10" height="10" class="mr-1" />
                                {{ tag }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="my-2 mt-4 border-t border-gray-200 dark:border-gray-700"></div>

                <!-- Bottom actions -->
                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                    <a
                        :href="props.resource.page_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-1 hover:text-primary transition-colors"
                    >
                        <Icon icon="mdi:external-link" width="16" height="16" />
                        Visit Resource
                    </a>

                    <div
                        v-if="props.resource.review_summary?.overall_rating > 0"
                        class="flex items-center gap-1"
                    >
                        <Icon icon="mdi:star" width="16" height="16" class="text-primary" />
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ Number(props.resource.review_summary?.overall_rating || 0).toFixed(1) }}
                        </span>
                        <span class="text-gray-500 dark:text-gray-400">
                            ({{ props.resource.review_summary?.review_count || 0 }} reviews)
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
