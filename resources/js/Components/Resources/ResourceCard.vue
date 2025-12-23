<script setup>
import { Icon } from "@iconify/vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import ClickableHeading from "@/Components/ClickableHeading.vue";
import ClickableImage from "@/Components/ClickableImage.vue";
import {
    difficultyLabels,
    pricingLabels,
    platformLabels,
} from "@/Helpers/labels";
import { platformIcons, pricingIcons, difficultyIcons } from "@/Helpers/icons";
import ResourceThumbnail from "./ResourceThumbnail.vue";

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["upvote", "downvote"]);
</script>

<template>
    <div
        class="rounded-sm border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 shadow-sm overflow-hidden transition-shadow hover:shadow-md"
    >
        <div class="flex flex-col sm:flex-row gap-3 p-3 sm:p-4">
            <!-- Mobile: Image and Vote Row -->
            <div class="flex sm:hidden gap-6 items-start">
                <!-- Vote section (mobile) -->
                <div class="flex flex-col items-center gap-1">
                    <Upvotable
                        :upvotable-id="resource.id"
                        :upvotable-key="'resource'"
                        :initial-votes="resource.vote_score"
                        :user-vote="resource.user_vote"
                        class="flex flex-col items-center"
                    />
                </div>

                <!-- Image section (mobile) -->
                <div class="shrink-0">
                    <ClickableImage
                        :href="route('resources.show', { slug: resource.slug })"
                    >
                        <ResourceThumbnail
                            :src="resource.image_url"
                            :alt="resource.name"
                        />
                    </ClickableImage>
                </div>

            </div>

            <!-- Desktop: Vote section -->
            <div class="hidden sm:flex flex-col items-center gap-1 pt-1 my-auto">
                <Upvotable
                    :upvotable-id="resource.id"
                    :upvotable-key="'resource'"
                    :initial-votes="resource.vote_score"
                    :user-vote="resource.user_vote"
                    class="flex flex-col items-center"
                />
            </div>

            <!-- Desktop: Image section -->
            <div class="hidden sm:block shrink-0 my-auto mr-4">
                <ClickableImage
                    :href="route('resources.show', { slug: resource.slug })"
                >
                    <ResourceThumbnail
                        :src="resource.image_url"
                        :alt="resource.name"
                    />
                </ClickableImage>
            </div>

            <!-- Content section -->
            <div class="flex-1 min-w-0">
                <!-- Primary info row -->
                <div class="flex items-center gap-2 mb-1">
                    <Icon
                        icon="mdi:star"
                        width="14"
                        height="14"
                        class="text-primary"
                    />
                    <div class="flex items-center gap-3 flex-wrap">
                        <!-- Top topics -->
                        <div class="flex items-center gap-1">
                            <span
                                v-for="topic in resource.topics_tags?.slice(
                                    0,
                                    4
                                )"
                                :key="topic"
                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-transparent"                            >
                                <Icon
                                    icon="mdi:lightbulb-outline"
                                    width="12"
                                    height="12"
                                    class="mr-1"
                                />
                                {{ topic }}
                            </span>
                        </div>

                        <!-- Difficulty -->
                        <div
                            v-if="resource.difficulties?.length"
                            class="flex items-center gap-1"
                        >
                            <span
                                v-for="difficulty in resource.difficulties"
                                :key="difficulty"
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-transparent text-primaryDark border border-primary/20"
                            >
                                <Icon
                                    :icon="difficultyIcons[difficulty]"
                                    width="12"
                                    height="12"
                                    class="mr-1"
                                />
                                {{ difficultyLabels[difficulty] }}
                            </span>
                        </div>

                        <!-- Pricing -->
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-transparent text-primaryDark border border-primary/20"
                        >
                            <Icon
                                :icon="pricingIcons[resource.pricing]"
                                width="12"
                                height="12"
                                class="mr-1"
                            />
                            {{ pricingLabels[resource.pricing] }}
                        </span>
                    </div>
                </div>

                <!-- Title -->
                <ClickableHeading
                    :href="route('resources.show', { slug: resource.slug })"
                >
                    <h3
                        class="text-lg font-semibold mb-2 hover:text-primary transition-colors text-balance"
                    >
                        {{ resource.name }}
                    </h3>
                </ClickableHeading>

                <!-- Description -->
                <p
                    class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2 text-pretty"
                >
                    {{ resource.description }}
                </p>

                <!-- Secondary info row -->
                <div class="flex items-center gap-1">
                    <span
                        v-for="platform in resource.platforms.slice(0, 2)"
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
                    <!-- Rest as comma-separated text -->
                    <span
                        v-if="
                            resource.programming_languages_tags?.length ||
                            resource.general_tags?.length
                        "
                        class="text-xs text-gray-600 dark:text-gray-400"
                    >
                        <template
                            v-if="resource.programming_languages_tags?.length"
                        >
                            •
                            {{ resource.programming_languages_tags.join(", ") }}
                        </template>

                        <template v-if="resource.general_tags?.length">
                            •
                            {{ resource.general_tags.join(", ") }}
                        </template>
                    </span>

                </div>
                <div
                    class="my-2 mt-4 border-t border-gray-200 dark:border-gray-700"
                ></div>

                <!-- Bottom actions -->
                <div
                    class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400"
                >
                    <a
                        :href="resource.page_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-1 hover:text-primary transition-colors"
                    >
                        <Icon icon="mdi:external-link" width="16" height="16" />
                        Visit Resource
                    </a>

                    <ClickableHeading
                        :href="
                            route('resources.show', { slug: resource.slug }) +
                            '#reviews'
                        "
                        class="flex items-center gap-1 hover:text-primary transition-colors"
                    >
                        <Icon
                            icon="mdi:star"
                            width="16"
                            height="16"
                            class="text-primary"
                        />
                        {{
                            Number(
                                resource.review_summary?.overall_rating || 0
                            ).toFixed(1)
                        }}
                        ({{
                            resource.review_summary?.review_count || 0
                        }}
                        reviews)
                    </ClickableHeading>
                </div>
            </div>
        </div>
    </div>
</template>
