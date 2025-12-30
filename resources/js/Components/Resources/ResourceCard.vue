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
import ResourceThumbnail from "@/Components/Resources/ResourceThumbnail.vue";
import StarRating from "@/Components/StarRating/StarRating.vue";
import Tag from "@/Components/Tag.vue";

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
                        icon="mdi:label-outline"
                        width="14"
                        height="14"
                        class="text-primary"
                    />
                    <div class="flex items-center gap-3 flex-wrap">
                        <!-- Top topics -->
                        <div class="flex items-center gap-1">
                            <Tag
                                v-for="topic in resource.topics_tags?.slice(
                                    0,
                                    4
                                )"
                                :key="topic"
                                :tag="topic"
                                icon="mdi:lightbulb-outline"
                                :icon-size="12"
                            />
                        </div>

                        <!-- Difficulty -->
                        <div
                            v-if="resource.difficulties?.length"
                            class="flex items-center gap-1"
                        >
                            <Tag
                                v-for="difficulty in resource.difficulties"
                                :key="difficulty"
                                :tag="difficultyLabels[difficulty]"
                                :icon="difficultyIcons[difficulty]"
                                :icon-size="12"
                            />
                        </div>

                        <!-- Pricing -->
                        <Tag
                            :tag="pricingLabels[resource.pricing]"
                            :icon="pricingIcons[resource.pricing]"
                            :icon-size="12"
                        />
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
                    class="text-sm text-gray-600 dark:text-gray-100 mb-3 line-clamp-2 text-pretty"
                >
                    {{ resource.description }}
                </p>

                <!-- Secondary info row -->
                <div class="flex items-center gap-1">
                    <Tag
                        v-for="platform in resource.platforms.slice(0, 2)"
                        :key="platform"
                        :tag="platformLabels[platform]"
                        :icon="platformIcons[platform]"
                    />
                    <!-- Rest as comma-separated text -->
                    <span
                        v-if="
                            resource.programming_languages_tags?.length ||
                            resource.general_tags?.length
                        "
                        class="text-xs text-gray-600 dark:text-gray-100"
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
                    class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-100"
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
                        <StarRating
                            :model-value="
                                Number(
                                    resource.review_summary
                                        ?.overall_rating
                                )
                            "
                            :size="16"
                        />
                        <span>
                            ({{ resource.review_summary?.review_count || 0 }} reviews)
                        </span>
                    </ClickableHeading>
                </div>
            </div>
        </div>
    </div>
</template>
