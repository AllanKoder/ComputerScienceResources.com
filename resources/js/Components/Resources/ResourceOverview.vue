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
import StarRating from "@/Components/StarRating/StarRating.vue";

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <!-- Header Section -->
    <div
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-sm shadow-sm px-3 sm:px-4 py-3 sm:py-4"
    >
        <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4">
            <!-- Mobile: Image and Vote Row -->
            <div class="flex sm:hidden gap-3 items-start w-full">
                <!-- Thumbnail -->
                <div class="shrink-0">
                    <ResourceThumbnail
                        :src="props.resource.image_url"
                        :alt="props.resource.name"
                    />
                </div>

                <!-- Vote Section (mobile) -->
                <UpvoteResource
                    :upvotable-id="props.resource.id"
                    :upvotable-key="'resource'"
                    :initial-votes="props.resource.vote_score"
                    :user-vote="props.resource.user_vote"
                    class="flex flex-col pt-1"
                />
            </div>

            <!-- Desktop: Image Section -->
            <div class="hidden sm:flex shrink-0 flex-row my-auto gap-3">
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
            <div class="flex-1 min-w-0 w-full sm:w-auto">
                <!-- Title -->
                <h1
                    class="text-2xl font-bold mb-2 text-gray-900 dark:text-gray-100 text-balance"
                >
                    {{ props.resource.name }}
                </h1>

                <!-- Description -->
                <p
                    class="text-sm text-gray-600 dark:text-gray-400 mb-3 leading-relaxed whitespace-pre-line"
                >
                    {{ props.resource.description }}
                </p>

                <!-- Two Column Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <!-- Left Column - Primary Info -->
                    <div
                        class="space-y-2 rounded-md p-3 border border-gray-200 dark:border-gray-700"
                    >
                        <!-- Topics -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <Icon
                                icon="mdi:lightbulb-outline"
                                width="12"
                                height="12"
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <span
                                class="text-xs font-semibold text-gray-600 dark:text-gray-400"
                                >Topics:</span
                            >
                            <span
                                v-for="topic in props.resource.topics_tags"
                                :key="topic"
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-transparent"
                            >
                                {{ topic }}
                            </span>
                        </div>

                        <!-- Difficulty -->
                        <div
                            v-if="props.resource.difficulties?.length"
                            class="flex items-center gap-1.5 flex-wrap"
                        >
                            <Icon
                                :icon="
                                    difficultyIcons[
                                        props.resource.difficulties[0]
                                    ]
                                "
                                width="12"
                                height="12"
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <span
                                class="text-xs font-semibold text-gray-600 dark:text-gray-400"
                                >Difficulty:</span
                            >
                            <span
                                v-for="difficulty in props.resource.difficulties"
                                :key="difficulty"
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-transparent"
                            >
                                <Icon
                                    :icon="difficultyIcons[difficulty]"
                                    width="10"
                                    height="10"
                                    class="mr-1"
                                />
                                {{ difficultyLabels[difficulty] }}
                            </span>
                        </div>

                        <!-- Pricing -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <Icon
                                :icon="pricingIcons[props.resource.pricing]"
                                width="12"
                                height="12"
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <span
                                class="text-xs font-semibold text-gray-600 dark:text-gray-400"
                                >Pricing:</span
                            >
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-transparent"
                            >
                                <Icon
                                    :icon="pricingIcons[props.resource.pricing]"
                                    width="10"
                                    height="10"
                                    class="mr-1"
                                />
                                {{ pricingLabels[props.resource.pricing] }}
                            </span>
                        </div>
                    </div>

                    <!-- Right Column - Secondary Info -->
                    <div
                        class="space-y-2 rounded-md p-3 border border-gray-200 dark:border-gray-700"
                    >
                        <!-- Platforms -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <Icon
                                icon="mdi:devices"
                                width="12"
                                height="12"
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <span
                                class="text-xs font-semibold text-gray-600 dark:text-gray-400"
                                >Platforms:</span
                            >
                            <span
                                v-for="platform in props.resource.platforms"
                                :key="platform"
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-transparent"
                            >
                                <Icon
                                    :icon="platformIcons[platform]"
                                    width="10"
                                    height="10"
                                    class="mr-1"
                                />
                                {{ platformLabels[platform] }}
                            </span>
                        </div>

                        <!-- Languages -->
                        <div
                            v-if="
                                props.resource.programming_languages_tags
                                    ?.length
                            "
                            class="flex items-center gap-1.5 flex-wrap"
                        >
                            <Icon
                                icon="mdi:code-tags"
                                width="12"
                                height="12"
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <span
                                class="text-xs font-semibold text-gray-600 dark:text-gray-400"
                                >Languages:</span
                            >
                            <span
                                v-for="language in props.resource.programming_languages_tags"
                                :key="language"
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-transparent"
                            >
                                {{ language }}
                            </span>
                        </div>

                        <!-- Tags -->
                        <div
                            v-if="props.resource.general_tags?.length"
                            class="flex items-center gap-1.5 flex-wrap"
                        >
                            <Icon
                                icon="mdi:label-outline"
                                width="12"
                                height="12"
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <span
                                class="text-xs font-semibold text-gray-600 dark:text-gray-400"
                                >Tags:</span
                            >
                            <span
                                v-for="tag in props.resource.general_tags"
                                :key="tag"
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-transparent"
                            >
                                {{ tag }}
                            </span>
                        </div>
                    </div>
                </div>

                <div
                    class="my-2 mt-4 border-t border-gray-200 dark:border-gray-700"
                ></div>

                <!-- Bottom actions -->
                <div
                    class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400"
                >
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
                        <StarRating
                            :model-value="
                                Number(
                                    props.resource.review_summary
                                        ?.overall_rating
                                )
                            "
                            :size="16"
                        />
                        <span class="text-gray-500 dark:text-gray-400">
                            ({{
                                props.resource.review_summary?.review_count || 0
                            }}
                            reviews)
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
