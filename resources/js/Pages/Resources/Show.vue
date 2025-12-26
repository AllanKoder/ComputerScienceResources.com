<script setup>
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import ResourceOverview from "@/Components/Resources/ResourceOverview.vue";
import ResourceDetailedRatings from "@/Components/Resources/ResourceDetailedRatings.vue";
import UserProfile from "@/Components/Profile/UserProfile.vue";
import ResourceTabs from "@/Components/Resources/ResourceTabs.vue";
import {
    resourceCanonical,
    summarize,
    ogImageForResource,
} from "@/Helpers/seo";

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
    sortingType: {
        type: String,
        required: false,
    },
});

const emit = defineEmits(["upvote", "downvote"]);

const urlParams = new URLSearchParams(window.location.search);
const sortingType = props.sortingType || urlParams.get("sort_by") || "top";
</script>

<template>
    <AppLayout :title="props.resource.name">
        <!-- Page metadata -->
        <Head>
            <meta
                head-key="resource:og:title"
                property="og:title"
                :content="props.resource.name"
            />
            <meta
                head-key="resource:description"
                name="description"
                :content="summarize(props.resource.description)"
            />
            <meta
                head-key="resource:og:description"
                property="og:description"
                :content="summarize(props.resource.description)"
            />
            <meta
                head-key="resource:og:type"
                property="og:type"
                content="article"
            />
            <meta
                head-key="resource:og:image"
                property="og:image"
                :content="ogImageForResource(props.resource.image_url)"
            />
            <link
                head-key="resource:canonical"
                rel="canonical"
                :href="resourceCanonical(props.resource.slug)"
            />
            <meta
                head-key="resource:og:url"
                property="og:url"
                :content="resourceCanonical(props.resource.slug)"
            />
            <meta
                head-key="resource:og:site_name"
                property="og:site_name"
                content="Computer Science Resources"
            />
        </Head>
        <div class="max-w-7xl mx-auto sm:px-6 py-4 lg:px-8">
            <!-- Main Resource Card -->
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg border border-primary/10 dark:border-primary/20 mb-4">
                <!-- Resource Overview -->
                <ResourceOverview :resource="props.resource" />

                <!-- Resource Ratings and Meta -->
                <div class="px-4 py-7 flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-4 border-t border-gray-200 dark:border-gray-700">
                    <ResourceDetailedRatings :review-summary="props.resource.review_summary || {}" />
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-right min-w-[160px] shrink-0">
                        <UserProfile
                            :user="resource.user"
                            :date="resource.created_at"
                        />
                    </div>
                </div>
            </div>

            <!-- Resource Tabs -->
            <ResourceTabs
                :tab="props.tab"
                :resource="props.resource"
                :discussion="props.discussion"
                :reviews="props.reviews"
                :user-review="props.userReview"
                :resource-edits="props.resourceEdits"
                :sorting-type="sortingType"
            />
        </div>
    </AppLayout>
</template>
