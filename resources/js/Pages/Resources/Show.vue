<script setup>
import { Deferred, Link, Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import UpvoteResource from "@/Components/Upvote/Upvotable.vue";
import ResourceReviews from "@/Components/Resources/Reviews/ResourceReviews.vue";
import ToggleCreateReview from "@/Components/Resources/Reviews/ToggleCreateReview.vue";
import Commentable from "@/Components/Comments/Commentable.vue";
import ResourceEdits from "@/Components/Resources/ResourceEdit/ResourceEdits.vue";
import ResourceUpvoteSorting from "@/Components/Resources/ResourceUpvoteSorting.vue";
import { getConfigData } from "@/Helpers/config";
import LoadingAnimation from "@/Components/LoadingAnimation.vue";
import UserProfile from "@/Components/Profile/UserProfile.vue";
import ProposeEditsButton from "@/Components/Resources/ResourceEdit/ProposeEditsButton.vue";
import ResourceThumbnail from "@/Components/Resources/ResourceThumbnail.vue";
import ResourceEditsFAQ from "@/Components/Resources/ResourceEdit/ResourceEditsFAQ.vue";
import ResourceDescription from "@/Components/Resources/ResourceDescription.vue";
import ResourceDetailedRatings from "@/Components/Resources/ResourceDetailedRatings.vue";
import { resourceCanonical, summarize, ogImageForResource, SITE_NAME } from "@/Helpers/seo";

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
        <!-- Page metadata -->
        <Head>
            <meta head-key="resource:og:title" property="og:title" :content="props.resource.name" />
            <meta head-key="resource:description" name="description" :content="summarize(props.resource.description)" />
            <meta head-key="resource:og:description" property="og:description" :content="summarize(props.resource.description)" />
            <meta head-key="resource:og:type" property="og:type" content="article" />
            <meta head-key="resource:og:image" property="og:image" :content="ogImageForResource(props.resource.image_url)" />
            <link head-key="resource:canonical" rel="canonical" :href="resourceCanonical(props.resource.slug)" />
            <meta head-key="resource:og:url" property="og:url" :content="resourceCanonical(props.resource.slug)" />
            <meta head-key="resource:og:site_name" property="og:site_name" content="Computer Science Resources" />
        </Head>
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
                            <ResourceDescription :resource="props.resource" />
                        </div>
                        <!-- Resource Link and Meta -->
                        <div
                            class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center"
                        >
                            <ResourceDetailedRatings :review-summary="props.resource.review_summary || {}" />
                            <div
                                class="text-xs m-6 text-gray-500 text-right min-w-[160px] shrink-0"
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
                                    slug: props.resource.slug,
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
                        :resource-slug="props.resource.slug"
                        :initial-value="sortingType"
                        :tab="props.tab"
                    ></ResourceUpvoteSorting>

                    <div v-if="props.tab === 'reviews'">
                        <ToggleCreateReview
                            :user-review="userReview"
                            :resource-id="props.resource.id"
                            :resource-slug="props.resource.slug"
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
                        <ProposeEditsButton :resource-slug="props.resource.slug" />
                        <ResourceEditsFAQ class="mb-5" />
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
