<script setup>
import { Icon } from "@iconify/vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import StarRating from "@/Components/StarRating/StarRating.vue";
import { Link } from "@inertiajs/vue3";
import { difficultyLabels, pricingLabels, platformLabels } from "@/Helpers/labels";
import { platformIcons, pricingIcons, difficultyIcons } from "@/Helpers/icons";
import ResourceThumbnail from "./ResourceThumbnail.vue";

defineProps({
    resource: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["upvote", "downvote"]);
</script>

<template>
    <div class="rounded-lg hover:bg-secondary/50 dark:hover:bg-gray-700/50 transition-colors duration-200 flex flex-col sm:flex-row w-full">
        <!-- Upvote and Image section (mobile friendly, centered) -->
        <div class="flex flex-row items-center justify-center sm:justify-start sm:items-center gap-2 sm:gap-4 p-3 sm:w-auto">
            <Upvotable
                :upvotable-id="resource.id"
                :upvotable-key="'resource'"
                :initial-votes="resource.vote_score"
                :user-vote="resource.user_vote"
            />
            <ResourceThumbnail
                :src="resource.image_url"
                :alt="resource.name"
                class="w-auto h-16 object-cover rounded-md sm:w-20 sm:h-20"
            />
        </div>

        <!-- Main content section -->
        <div class="flex-1 flex flex-col justify-between py-3 px-2">
            <div class="flex flex-col lg:flex-row justify-between items-start mb-2 w-full gap-2">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('resources.show', { slug: resource.slug })"
                        class="group"
                    >
                        <h2 class="text-lg underline font-semibold tracking-tight text-gray-900 dark:text-gray-100 group-hover:text-primary transition-colors duration-200 font-sans">
                            {{ resource.name }}
                        </h2>
                    </Link>
                    <a
                        :href="resource.page_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-gray-400 hover:text-primary transition-colors duration-200"
                    >
                        <Icon icon="mdi:external-link" width="20" height="20" />
                    </a>
                </div>
                <div class="flex items-center gap-4 flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <StarRating
                            :model-value="Number(resource.review_summary?.overall_rating)"
                            :size="20"
                        />
                        <div class="inline-flex items-center gap-0.5 text-xs font-medium text-gray-700 dark:text-gray-300">
                            <Icon icon="mdi:account-group" width="16" height="16" />
                            {{ resource.review_summary?.review_count || 0 }}
                        </div>
                    </div>
                    <time class="text-xs text-gray-500 font-medium">{{ resource.resource_created_on }}</time>
                </div>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mb-2 line-clamp-2 leading-relaxed text-sm">{{ resource.description }}</p>

            <!-- Resource Metadata Section -->
            <div class="flex flex-col gap-2">
                <!-- Row 1: Resource Properties -->
                <div class="flex flex-wrap items-center gap-3 text-xs">
                    <!-- Difficulty -->
                    <div v-if="resource.difficulty" class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-800/50 px-2 py-1 rounded-md">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">Level:</span>
                        <span class="inline-flex items-center gap-1 text-gray-700 dark:text-gray-300">
                            <Icon :icon="difficultyIcons[resource.difficulty]" width="14" height="14" />
                            {{ difficultyLabels[resource.difficulty] }}
                        </span>
                    </div>

                    <!-- Pricing -->
                    <div class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-800/50 px-2 py-1 rounded-md">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">Pricing:</span>
                        <span class="inline-flex items-center gap-1 text-gray-700 dark:text-gray-300">
                            <Icon :icon="pricingIcons[resource.pricing]" width="14" height="14" />
                            {{ pricingLabels[resource.pricing] }}
                        </span>
                    </div>

                    <!-- Platforms -->
                    <div class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-800/50 px-2 py-1 rounded-md">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">Platforms:</span>
                        <div class="inline-flex flex-wrap items-center gap-2 text-gray-700 dark:text-gray-300">
                            <span v-for="type in resource.platforms" :key="type" class="inline-flex items-center gap-1">
                                <Icon :icon="platformIcons[type.trim()]" width="14" height="14" />
                                {{ platformLabels[type.trim()] }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Tags -->
                <div class="flex flex-wrap items-center gap-3 text-xs">
                    <!-- Topic Tags -->
                    <div v-if="resource.topic_tags?.length" class="flex items-center gap-1 px-2 py-1 rounded-md">
                        <span class="inline-flex items-center gap-1 font-semibold text-blue-700 dark:text-blue-200">
                            <Icon icon="mdi:bookmark" width="14" height="14" />
                            Topics:
                        </span>
                        <div class="inline-flex flex-wrap items-center gap-0.5">
                            <span v-for="tag in resource.topic_tags" :key="tag"
                                class="inline-flex items-center gap-1 bg-blue-100/50 dark:bg-blue-800/30 px-2 py-0.5 rounded-full text-blue-700 dark:text-blue-200">
                                {{ tag }}
                            </span>
                        </div>
                    </div>

                    <!-- Programming Language Tags -->
                    <div v-if="resource.programming_language_tags?.length" class="flex items-center gap-1.5 px-2 py-1 rounded-md">
                        <span class="inline-flex items-center gap-1 font-semibold text-purple-700 dark:text-purple-200">
                            <Icon icon="mdi:language-typescript" width="14" height="14" />
                            Languages:
                        </span>
                        <div class="inline-flex flex-wrap items-center gap-0.5">
                            <span v-for="tag in resource.programming_language_tags" :key="tag"
                                class="inline-flex items-center gap-1 bg-purple-100/50 dark:bg-purple-800/30 px-2 py-0.5 rounded-full text-purple-700 dark:text-purple-200">
                                {{ tag }}
                            </span>
                        </div>
                    </div>

                    <!-- General Tags -->
                    <div v-if="resource.general_tags?.length" class="flex items-center gap-1.5 px-2 py-1 rounded-md">
                        <span class="inline-flex items-center gap-1 font-semibold text-yellow-700 dark:text-yellow-200">
                            <Icon icon="mdi:tag" width="14" height="14" />
                            Tags:
                        </span>
                        <div class="inline-flex flex-wrap items-center gap-0.5">
                            <span v-for="tag in resource.general_tags" :key="tag"
                                class="inline-flex items-center gap-1 bg-yellow-100/50 dark:bg-yellow-800/30 px-2 py-0.5 rounded-full text-yellow-700 dark:text-yellow-200">
                                {{ tag }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
