<script setup>
import { Deferred, Link } from "@inertiajs/vue3";
import { Icon } from "@iconify/vue";
import ResourceReviews from "@/Components/Resources/Reviews/ResourceReviews.vue";
import ToggleCreateReview from "@/Components/Resources/Reviews/ToggleCreateReview.vue";
import Commentable from "@/Components/Comments/Commentable.vue";
import ResourceEdits from "@/Components/Resources/ResourceEdit/ResourceEdits.vue";
import ResourceUpvoteSorting from "@/Components/Resources/ResourceUpvoteSorting.vue";
import { getConfigData } from "@/Helpers/config";
import LoadingAnimation from "@/Components/LoadingAnimation.vue";
import ProposeEditsButton from "@/Components/Resources/ResourceEdit/ProposeEditsButton.vue";
import ResourceEditsFAQ from "@/Components/Resources/ResourceEdit/ResourceEditsFAQ.vue";

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
    discussionSortByValue: {
        type: String,
        required: false,
    },
    sortingType: {
        type: String,
        required: true,
    },
});

const tabs = [
    { label: "Reviews", value: "reviews" },
    { label: "Discussion", value: "discussion" },
    { label: "Proposed Edits", value: "edits" },
];
</script>

<template>
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Tab Navigation Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <div class="flex space-x-8 px-6">
                <Link
                    v-for="tabItem in tabs"
                    :key="tabItem.value"
                    :except="['resource']"
                    preserve-scroll
                    preserve-state
                    prefetch
                    cache-for="10s"
                    :href="route('resources.show', { slug: props.resource.slug, tab: tabItem.value })"
                    :class="[
                        'py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200',
                        props.tab === tabItem.value
                            ? 'border-primary text-primary dark:text-primaryLight'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300',
                    ]"
                >
                    <div class="flex items-center gap-2">
                        <Icon
                            :icon="tabItem.value === 'reviews' ? 'mdi:star' : tabItem.value === 'discussion' ? 'mdi:chat-processing' : 'mdi:pencil'"
                            class="w-4 h-4"
                        />
                        {{ tabItem.label }}
                    </div>
                </Link>
            </div>
        </div>

        <!-- Sorting Controls -->
        <div class="px-6 border-b border-gray-200 dark:border-gray-700 ">
            <ResourceUpvoteSorting
                :resource-slug="props.resource.slug"
                :initial-value="props.sortingType"
                :tab="props.tab"
            />
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Reviews Tab -->
            <div v-if="props.tab === 'reviews'" class="space-y-6">
                <ToggleCreateReview
                    :user-review="props.userReview"
                    :resource-id="props.resource.id"
                    :resource-slug="props.resource.slug"
                />
                <Deferred data="reviews">
                    <template #fallback>
                        <div class="flex justify-center py-12">
                            <LoadingAnimation />
                        </div>
                    </template>
                    <ResourceReviews :reviews="props.reviews" />
                </Deferred>
            </div>

            <!-- Discussion Tab -->
            <div v-else-if="props.tab === 'discussion'" class="space-y-6">
                <Deferred data="discussion">
                    <template #fallback>
                        <div class="flex justify-center py-12">
                            <LoadingAnimation />
                        </div>
                    </template>
                    <Commentable
                        :sort-by-initial-value="props.discussionSortByValue"
                        :has-sort-by-dropdown="false"
                        :commentable-id="props.resource.id"
                        :commentable-key="'resource'"
                        :comments-count="props.resource.comments_count"
                        :loaded-comment-data="props.discussion"
                        :pagination-limit="getConfigData().COMMENT_PAGINATION_LIMIT"
                    />
                </Deferred>
            </div>

            <!-- Edits Tab -->
            <div v-else-if="props.tab === 'edits'" class="space-y-6">
                <ProposeEditsButton :resource-slug="props.resource.slug" />
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <ResourceEditsFAQ />
                </div>
                <Deferred data="resourceEdits">
                    <template #fallback>
                        <div class="flex justify-center py-12">
                            <LoadingAnimation />
                        </div>
                    </template>
                    <ResourceEdits
                        :resource-id="props.resource.id"
                        :resource-edits="props.resourceEdits"
                    />
                </Deferred>
            </div>
        </div>
    </div>
</template>
