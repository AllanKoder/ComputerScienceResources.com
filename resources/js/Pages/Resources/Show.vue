<script setup>
import { Head, Deferred, Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import UpvoteResource from "@/Components/Upvote/Upvotable.vue";
import { pricingLabels, difficultyLabels } from "@/Helpers/labels.js";
import ResourceReviews from "@/Components/Resources/Reviews/ResourceReviews.vue";
import ToggleCreateReview from "@/Components/Resources/Reviews/ToggleCreateReview.vue";
import Commentable from "@/Components/Comments/Commentable.vue";
import ResourceEdits from "@/Components/Resources/ResourceEdit/ResourceEdits.vue";
import ResourceUpvoteSorting from "@/Components/Resources/ResourceUpvoteSorting.vue";
import { getConfigData } from "@/Helpers/config";
import StarRating from "@/Components/StarRating/StarRating.vue";
import { Icon } from "@iconify/vue";
import { platformIcons, pricingIcons, difficultyIcons } from "@/Helpers/icons";
import { platformLabels } from "@/Helpers/labels";
import LoadingAnimation from "@/Components/LoadingAnimation.vue";
import UserProfile from "@/Components/Profile/UserProfile.vue";
import ProposeEditsButton from "@/Components/Resources/ResourceEdit/ProposeEditsButton.vue";
import ResourceThumbnail from "@/Components/Resources/ResourceThumbnail.vue";

const props = defineProps({
    tab: {
        type: String,
        required: true,
    },
    resource: {
        type: Object,
        required: true,
    },
    discussion: {
        type: Object,
        required: false,
    },
    // Paginated
    reviews: {
        type: Object,
        required: false,
    },
    userReview: {
        type: Object,
        required: false,
    },
    resourceEdits: {
        type: Object,
        required: false,
    },
});

const emit = defineEmits(["upvote", "downvote"]);

const tabs = [
    { label: "Reviews", value: "reviews" },
    { label: "Discussion", value: "discussion" },
    { label: "Proposed Edits", value: "edits" },
];

const urlParams = new URLSearchParams(window.location.search);
const sortingType = urlParams.get("sort_by") || "top";
</script>

<template>
    <AppLayout :title="props.resource.name">
        <div class="max-w-[90vw] mx-auto sm:px-6 py-4 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-7 sm:p-8">
                    <div class="relative">
                        <div
                            class="flex flex-col md:flex-row items-center mb-2 gap-6"
                        >
                            <!-- Upvote column -->
                            <div class="flex flex-row items-center mr-6 gap-3">
                                <UpvoteResource
                                    :upvotable-id="props.resource.id"
                                    :upvotable-key="'resource'"
                                    :initial-votes="props.resource.vote_score"
                                    :user-vote="props.resource.user_vote"
                                />

                                <!-- Image column -->
                                <ResourceThumbnail
                                    :src="props.resource.image_url"
                                    :alt="props.resource.name"
                                />
                            </div>

                            <!-- Main content column -->
                            <div class="flex-grow w-full">
                                <div
                                    class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-4 gap-4"
                                >
                                    <div class="flex items-center gap-3">
                                        <h1 class="text-3xl font-bold">
                                            {{ props.resource.name }}
                                        </h1>
                                        <a
                                            :href="props.resource.page_url"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-gray-400 hover:text-primary transition-colors duration-200"
                                        >
                                            <Icon
                                                icon="mdi:external-link"
                                                width="24"
                                                height="24"
                                            />
                                        </a>
                                    </div>
                                    <div class="flex flex-col gap-4">
                                        <!-- Overall Rating with Review Count -->
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <StarRating
                                                    :model-value="
                                                        Number(
                                                            props.resource
                                                                .review_summary
                                                                ?.overall_rating
                                                        )
                                                    "
                                                    :size="23"
                                                />
                                            </div>
                                            <div
                                                class="inline-flex items-center gap-1 text-lg font-medium text-gray-700 dark:text-gray-300"
                                            >
                                                <Icon
                                                    icon="mdi:account-group"
                                                    width="20"
                                                    height="20"
                                                />
                                                {{
                                                    props.resource
                                                        .review_summary
                                                        ?.review_count || 0
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <p
                                    class="text-gray-700 mb-6 text-base leading-relaxed"
                                >
                                    {{ props.resource.description }}
                                </p>

                                <!-- Resource Metadata Section -->
                                <div class="flex flex-col gap-3 mb-4">
                                    <!-- Row 1: Resource Properties -->
                                    <div
                                        class="flex flex-wrap items-center gap-4 text-sm"
                                    >
                                        <!-- Difficulty -->
                                        <div
                                            v-if="props.resource.difficulty"
                                            class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-800/50 px-3 py-1 rounded-md"
                                        >
                                            <span
                                                class="font-semibold text-gray-900 dark:text-gray-100"
                                                >Level:</span
                                            >
                                            <span
                                                class="inline-flex items-center gap-1 text-gray-700 dark:text-gray-300"
                                            >
                                                <Icon
                                                    :icon="
                                                        difficultyIcons[
                                                            props.resource
                                                                .difficulty
                                                        ]
                                                    "
                                                    width="16"
                                                    height="16"
                                                />
                                                {{
                                                    difficultyLabels[
                                                        props.resource
                                                            .difficulty
                                                    ]
                                                }}
                                            </span>
                                        </div>

                                        <!-- Pricing -->
                                        <div
                                            class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-800/50 px-3 py-1 rounded-md"
                                        >
                                            <span
                                                class="font-semibold text-gray-900 dark:text-gray-100"
                                                >Pricing:</span
                                            >
                                            <span
                                                class="inline-flex items-center gap-1 text-gray-700 dark:text-gray-300"
                                            >
                                                <Icon
                                                    :icon="
                                                        pricingIcons[
                                                            props.resource
                                                                .pricing
                                                        ]
                                                    "
                                                    width="16"
                                                    height="16"
                                                />
                                                {{
                                                    pricingLabels[
                                                        props.resource.pricing
                                                    ]
                                                }}
                                            </span>
                                        </div>

                                        <!-- Platforms -->
                                        <div
                                            class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-800/50 px-3 py-1 rounded-md"
                                        >
                                            <span
                                                class="font-semibold text-gray-900 dark:text-gray-100"
                                                >Platforms:</span
                                            >
                                            <div
                                                class="inline-flex flex-wrap items-center gap-2 text-gray-700 dark:text-gray-300"
                                            >
                                                <span
                                                    v-for="type in props
                                                        .resource.platforms"
                                                    :key="type"
                                                    class="inline-flex items-center gap-1"
                                                >
                                                    <Icon
                                                        :icon="
                                                            platformIcons[
                                                                type.trim()
                                                            ]
                                                        "
                                                        width="16"
                                                        height="16"
                                                    />
                                                    {{
                                                        platformLabels[
                                                            type.trim()
                                                        ]
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Row 2: Tags -->
                                    <div
                                        class="flex flex-wrap items-center gap-4 text-sm"
                                    >
                                        <!-- Topic Tags -->
                                        <div
                                            v-if="resource.topic_tags?.length"
                                            class="flex items-center gap-1 px-2 py-1 rounded-md"
                                        >
                                            <span
                                                class="inline-flex items-center gap-1 font-semibold text-blue-700 dark:text-blue-200"
                                            >
                                                <Icon
                                                    icon="mdi:bookmark"
                                                    width="14"
                                                    height="14"
                                                />
                                                Topics:
                                            </span>
                                            <div
                                                class="inline-flex flex-wrap items-center gap-0.5"
                                            >
                                                <span
                                                    v-for="tag in resource.topic_tags"
                                                    :key="tag"
                                                    class="inline-flex items-center gap-1 bg-blue-100/50 dark:bg-blue-800/30 px-2 py-0.5 rounded-full text-blue-700 dark:text-blue-200"
                                                >
                                                    {{ tag }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Programming Language Tags -->
                                        <div
                                            v-if="
                                                resource
                                                    .programming_language_tags
                                                    ?.length
                                            "
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
                                            <div
                                                class="inline-flex flex-wrap items-center gap-0.5"
                                            >
                                                <span
                                                    v-for="tag in resource.programming_language_tags"
                                                    :key="tag"
                                                    class="inline-flex items-center gap-1 bg-purple-100/50 dark:bg-purple-800/30 px-2 py-0.5 rounded-full text-purple-700 dark:text-purple-200"
                                                >
                                                    {{ tag }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- General Tags -->
                                        <div
                                            v-if="resource.general_tags?.length"
                                            class="flex items-center gap-1.5 px-2 py-1 rounded-md"
                                        >
                                            <span
                                                class="inline-flex items-center gap-1 font-semibold text-yellow-700 dark:text-yellow-200"
                                            >
                                                <Icon
                                                    icon="mdi:tag"
                                                    width="14"
                                                    height="14"
                                                />
                                                Tags:
                                            </span>
                                            <div
                                                class="inline-flex flex-wrap items-center gap-0.5"
                                            >
                                                <span
                                                    v-for="tag in resource.general_tags"
                                                    :key="tag"
                                                    class="inline-flex items-center gap-1 bg-yellow-100/50 dark:bg-yellow-800/30 px-2 py-0.5 rounded-full text-yellow-700 dark:text-yellow-200"
                                                >
                                                    {{ tag }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Resource Link and Meta -->
                        <div
                            class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-end"
                        >
                            <!-- Detailed Ratings Grid -->
                            <div class="flex-1 flex justify-center">
                                <div
                                    class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-7"
                                >
                                    <!-- Community Rating -->
                                    <div class="flex flex-col items-center">
                                        <label
                                            class="flex items-center gap-2 text-sm font-semibold text-gray-600 mb-2"
                                        >
                                            <Icon
                                                icon="mdi:account-group"
                                                class="w-4 h-4"
                                            />
                                            Community
                                        </label>
                                        <StarRating
                                            :model-value="
                                                Number(
                                                    props.resource
                                                        .review_summary
                                                        ?.community_rating
                                                )
                                            "
                                            :size="20"
                                        />
                                    </div>

                                    <!-- Teaching Clarity Rating -->
                                    <div class="flex flex-col items-center">
                                        <label
                                            class="flex items-center gap-2 text-sm font-semibold text-gray-600 mb-2"
                                        >
                                            <Icon
                                                icon="mdi:school"
                                                class="w-4 h-4"
                                            />
                                            Teaching
                                        </label>
                                        <StarRating
                                            :model-value="
                                                Number(
                                                    props.resource
                                                        .review_summary
                                                        ?.teaching_clarity_rating
                                                )
                                            "
                                            :size="20"
                                        />
                                    </div>

                                    <!-- Engagement Rating -->
                                    <div class="flex flex-col items-center">
                                        <label
                                            class="flex items-center gap-2 text-sm font-semibold text-gray-600 mb-2"
                                        >
                                            <Icon
                                                icon="mdi:thumb-up"
                                                class="w-4 h-4"
                                            />
                                            Engagement
                                        </label>
                                        <StarRating
                                            :model-value="
                                                Number(
                                                    props.resource
                                                        .review_summary
                                                        ?.engagement_rating
                                                )
                                            "
                                            :size="20"
                                        />
                                    </div>

                                    <!-- Practicality Rating -->
                                    <div class="flex flex-col items-center">
                                        <label
                                            class="flex items-center gap-2 text-sm font-semibold text-gray-600 mb-2"
                                        >
                                            <Icon
                                                icon="mdi:tools"
                                                class="w-4 h-4"
                                            />
                                            Practicality
                                        </label>
                                        <StarRating
                                            :model-value="
                                                Number(
                                                    props.resource
                                                        .review_summary
                                                        ?.practicality_rating
                                                )
                                            "
                                            :size="20"
                                        />
                                    </div>

                                    <!-- User Friendliness Rating -->
                                    <div class="flex flex-col items-center">
                                        <label
                                            class="flex items-center gap-2 text-sm font-semibold text-gray-600 mb-2"
                                        >
                                            <Icon
                                                icon="mdi:account-heart"
                                                class="w-4 h-4"
                                            />
                                            User Friendly
                                        </label>
                                        <StarRating
                                            :model-value="
                                                Number(
                                                    props.resource
                                                        .review_summary
                                                        ?.user_friendliness_rating
                                                )
                                            "
                                            :size="20"
                                        />
                                    </div>

                                    <!-- Updates Rating -->
                                    <div class="flex flex-col items-center">
                                        <label
                                            class="flex items-center gap-2 text-sm font-semibold text-gray-600 mb-2"
                                        >
                                            <Icon
                                                icon="mdi:update"
                                                class="w-4 h-4"
                                            />
                                            Updates
                                        </label>
                                        <StarRating
                                            :model-value="
                                                Number(
                                                    props.resource
                                                        .review_summary
                                                        ?.updates_rating
                                                )
                                            "
                                            :size="20"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div
                                class="text-xs m-6 text-gray-500 text-right space-y-1 min-w-[160px] shrink-0"
                            >
                                <UserProfile
                                    :user="resource.user"
                                    :date="resource.created_at"
                                ></UserProfile>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Tab Navigation -->
                <div class="flex border-b mb-4 space-x-6 px-6">
                    <div
                        v-for="tab in tabs"
                        :class="[
                            'py-2 border-b-2 font-medium transition-all duration-200',
                            props.tab === tab.value
                                ? 'border-primary text-primary'
                                : 'border-transparent text-gray-600',
                        ]"
                    >
                        <Link
                            :except="['resource']"
                            preserve-scroll
                            preserve-state
                            prefetch
                            cache-for="10s"
                            :href="
                                route('resources.show', {
                                    computerScienceResource: props.resource.id,
                                    tab: tab.value,
                                })
                            "
                        >
                            {{ tab.label }}
                        </Link>
                    </div>
                </div>

                <!-- Tab Panels -->
                <div class="px-6 pb-6">
                    <ResourceUpvoteSorting
                        :resource-id="props.resource.id"
                        :initial-value="sortingType"
                        :tab="props.tab"
                    ></ResourceUpvoteSorting>

                    <div v-if="props.tab === 'reviews'">
                        <ToggleCreateReview
                            :user-review="userReview"
                            :resource-id="props.resource.id"
                        />
                        <Deferred data="reviews">
                            <template #fallback>
                                <LoadingAnimation />
                            </template>
                            <ResourceReviews :reviews="reviews" />
                        </Deferred>
                    </div>

                    <div v-else-if="props.tab === 'discussion'">
                        <Deferred data="discussion">
                            <template #fallback>
                                <LoadingAnimation />
                            </template>
                            <Commentable
                                :sort-by-initial-value="
                                    props.discussionSortByValue
                                "
                                :has-sort-by-dropdown="false"
                                :commentable-id="props.resource.id"
                                :commentable-key="'resource'"
                                :comments-count="props.resource.comments_count"
                                :loaded-comment-data="discussion"
                                :pagination-limit="
                                    getConfigData().COMMENT_PAGINATION_LIMIT
                                "
                            />
                        </Deferred>
                    </div>

                    <div v-else-if="props.tab === 'edits'">
                        <ProposeEditsButton :resource-id="props.resource.id" />
                        <Deferred data="resourceEdits">
                            <template #fallback>
                                <LoadingAnimation />
                            </template>
                            <ResourceEdits
                                :resource-id="props.resource.id"
                                :resource-edits="resourceEdits"
                            />
                        </Deferred>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
