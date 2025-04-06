<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Tag from "primevue/tag";
import UpvoteResource from "@/Components/Upvote/Upvotable.vue";
import { pricingLabels, difficultyLabels, platformColors } from "@/Helpers/constants.js";
import ResourceReviews from "@/Components/Resources/Reviews/ResourceReviews.vue";
import ResourceTab from "@/Components/Resources/ResourceEdit/ResourceTab.vue";

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
    reviews: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["upvote", "downvote"]);

</script>

<template>
    <AppLayout :title="props.resource.name">
        <Head :title="props.resource.name" />
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:p-8">
                        <ResourceTab :resource-id="props.resource.id" />
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
                                            props.resource.total_votes
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
                                                pricingLabels[props.resource.pricing]
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        <Tag
                                            v-for="platform in props.resource.platforms"
                                            :key="platform"
                                            :value="platform"
                                            :severity="platformColors[platform]"
                                            class="capitalize"
                                        />
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        <Tag
                                            v-for="tag in props.resource.topic_tags"
                                            :key="tag"
                                            :value="tag"
                                            severity="info"
                                            class="text-xs"
                                        />
                                        <Tag
                                            v-for="tag in props.resource.programming_language_tags"
                                            :key="tag"
                                            :value="tag"
                                            severity="success"
                                            class="text-xs"
                                        />
                                        <Tag
                                            v-for="tag in props.resource.general_tags"
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
                                    {{ props.resource.user?.name ?? "Unknown User" }}
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

                    <!-- Reviews -->

                    <ResourceReviews
                        :reviews="props.reviews"
                        :resourceId="props.resource.id"
                    ></ResourceReviews>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
