<script setup>
import { Head, Deferred, Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Tag from "primevue/tag";
import UpvoteResource from "@/Components/Upvote/Upvotable.vue";
import {
    pricingLabels,
    difficultyLabels,
    platformColors,
} from "@/Helpers/labels.js";
import ResourceReviews from "@/Components/Resources/Reviews/ResourceReviews.vue";
import Commentable from "@/Components/Comments/Commentable.vue";
import ResourceEdits from "@/Components/Resources/ResourceEdit/ResourceEdits.vue";
import { getConfigData } from "@/Helpers/config";

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
        type: Array,
        required: false,
    },
    resourceEdits: {
        type: Array,
        required: false,
    },
});

const emit = defineEmits(["upvote", "downvote"]);

const tabs = [
    { label: "Reviews", value: "reviews" },
    { label: "Discussion", value: "discussion" },
    { label: "Proposed Edits", value: "edits" },
];
</script>

<template>
    <AppLayout :title="props.resource.name">
        <Head :title="props.resource.name" />
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:p-8">
                        <div class="relative">
                            <div
                                class="flex flex-col md:flex-row items-start mb-6"
                            >
                                <div
                                    class="flex flex-col items-center mr-4 mb-4 md:mb-0"
                                >
                                    <UpvoteResource
                                        :upvotable-id="props.resource.id"
                                        :upvotable-type="'resource'"
                                        :initial-votes="
                                            props.resource.vote_score
                                        "
                                        :user-vote="props.resource.user_vote"
                                    ></UpvoteResource>
                                </div>
                                <img
                                    :src="props.resource.image_url"
                                    :alt="props.resource.name"
                                    class="w-24 h-24 object-cover rounded-lg mr-6 mb-4 md:mb-0"
                                />
                                <div class="flex-grow">
                                    <h1 class="text-3xl font-bold mb-2">
                                        {{ props.resource.name }}
                                    </h1>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2"
                                    >
                                        <div>
                                            <span
                                                class="font-semibold text-gray-700"
                                                >Difficulty:</span
                                            >
                                            <span class="text-gray-600 ml-1">{{
                                                difficultyLabels[
                                                    props.resource.difficulty
                                                ]
                                            }}</span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-semibold text-gray-700"
                                                >Pricing:</span
                                            >
                                            <span class="text-gray-600 ml-1">{{
                                                pricingLabels[
                                                    props.resource.pricing
                                                ]
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        <Tag
                                            v-for="platform in props.resource
                                                .platforms"
                                            :key="platform"
                                            :value="platform"
                                            :severity="platformColors[platform]"
                                            class="capitalize"
                                        />
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        <Tag
                                            v-for="tag in props.resource
                                                .topic_tags"
                                            :key="tag"
                                            :value="tag"
                                            severity="info"
                                            class="text-xs"
                                        />
                                        <Tag
                                            v-for="tag in props.resource
                                                .programming_language_tags"
                                            :key="tag"
                                            :value="tag"
                                            severity="success"
                                            class="text-xs"
                                        />
                                        <Tag
                                            v-for="tag in props.resource
                                                .general_tags"
                                            :key="tag"
                                            :value="tag"
                                            severity="warning"
                                            class="text-xs"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-4">
                            {{ props.resource.description }}
                        </p>
                        <div
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4"
                        >
                            <a
                                :href="props.resource.page_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-blue-600 hover:underline mb-2 sm:mb-0"
                                >Visit Resource</a
                            >
                            <div class="text-sm text-gray-500">
                                <p>
                                    Posted by:
                                    {{
                                        props.resource.user?.name ??
                                        "Unknown User"
                                    }}
                                </p>
                                <p>
                                    Created:
                                    {{
                                        new Date(
                                            props.resource.created_at
                                        ).toLocaleString()
                                    }}
                                </p>
                                <p>
                                    Last updated:
                                    {{
                                        new Date(
                                            props.resource.updated_at
                                        ).toLocaleString()
                                    }}
                                </p>
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
                                    ? 'border-blue-600 text-blue-600'
                                    : 'border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600',
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
                                        computerScienceResource:
                                            props.resource.id,
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
                        <div v-if="props.tab === 'reviews'">
                            <Deferred data="reviews">
                                <template #fallback>
                                    <div>Loading...</div>
                                </template>
                                <ResourceReviews
                                    :reviews="reviews"
                                    :resource-id="props.resource.id"
                                />
                            </Deferred>
                        </div>

                        <div v-else-if="props.tab === 'discussion'">
                            <Deferred data="discussion">
                                <template #fallback>
                                    <div>Loading...</div>
                                </template>
                                <Commentable
                                    :commentable-id="props.resource.id"
                                    :commentable-type="'resource'"
                                    :comments-count="
                                        props.resource.comments_count
                                    "
                                    :loaded-comment-data="discussion"
                                    :pagination-limit="getConfigData().COMMENT_PAGINATION_LIMIT"
                                    />
                            </Deferred>
                        </div>

                        <div v-else-if="props.tab === 'edits'">
                            <Deferred data="resourceEdits">
                                <template #fallback>
                                    <div>Loading...</div>
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
        </main>
    </AppLayout>
</template>
