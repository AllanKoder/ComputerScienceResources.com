<script setup>
import { Icon } from "@iconify/vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import StarRating from "@/Components/StarRating/StarRating.vue";
import { Link } from "@inertiajs/vue3";
import { difficultyLabels, pricingLabels } from "@/Helpers/labels";
import { platformIcons, pricingIcons, difficultyIcons } from "@/Helpers/icons";

defineProps({
    resource: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["upvote", "downvote"]);
</script>

<template>
    <tr class="rounded-lg hover:bg-secondary/50 dark:hover:bg-gray-700/50 transition-colors duration-200">
        <!-- Upvote column -->
        <td class="align-middle pr-4 pl-3 py-3">
            <Upvotable
                :upvotable-id="resource.id"
                :upvotable-key="'resource'"
                :initial-votes="resource.vote_score"
                :user-vote="resource.user_vote"
            ></Upvotable>
        </td>

        <!-- Image column -->
        <td class="align-middle pr-4 py-3 w-24">
            <img
                :src="resource.image_url"
                :alt="resource.name"
                class="w-full h-auto object-contain rounded-lg shadow-sm"
            />
        </td>

        <!-- Main content column -->
        <td class="align-top pr-4 py-3 min-w-[400px]">
            <div class="flex justify-between items-start mb-2">
                <Link
                    :href="route('resources.show', { computerScienceResource: resource.id })"
                    class="group"
                >
                    <h2 class="text-lg font-semibold tracking-tight text-gray-900 dark:text-gray-100 group-hover:text-primary transition-colors duration-200 font-sans">
                        {{ resource.name }}
                    </h2>
                </Link>
                <time class="text-xs text-gray-500 font-medium">{{ resource.resource_created_on }}</time>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mb-2 line-clamp-2 leading-relaxed text-sm">{{ resource.description }}</p>

            <!-- All Tags Section -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Platforms -->
                <div class="flex flex-wrap items-center gap-1">
                    <span v-for="type in resource.platforms" :key="type"
                        class="inline-flex items-center gap-1 bg-primary/10 dark:bg-primary/20 px-2 py-1 rounded-full text-xs font-medium text-primary-dark dark:text-secondary">
                        <Icon :icon="platformIcons[type.trim()]" width="14" height="14" />
                        {{ type.trim() }}
                    </span>
                </div>

                <span class="text-gray-300 dark:text-gray-600">|</span>

                <!-- Pricing -->
                <div class="inline-flex items-center gap-1 bg-green-100 dark:bg-green-900/50 px-2 py-1 rounded-full">
                    <Icon :icon="pricingIcons[resource.pricing]" width="14" height="14" class="text-green-800 dark:text-green-200" />
                    <span class="text-xs font-medium text-green-800 dark:text-green-200">
                        {{ pricingLabels[resource.pricing] }}
                    </span>
                </div>

                <span class="text-gray-300 dark:text-gray-600">|</span>

                <!-- Difficulty -->
                <div v-if="resource.difficulty" class="inline-flex items-center gap-1 bg-red-100 dark:bg-red-900/50 px-2 py-1 rounded-full">
                    <Icon :icon="difficultyIcons[resource.difficulty]" width="14" height="14" class="text-red-800 dark:text-red-200" />
                    <span class="text-xs font-medium text-red-800 dark:text-red-200">
                        {{ difficultyLabels[resource.difficulty] }}
                    </span>
                </div>

                <span class="text-gray-300 dark:text-gray-600">|</span>

                <!-- Topic Tags -->
                <div v-if="resource.topic_tags?.length" class="flex flex-wrap items-center gap-1">
                    <span v-for="(tag, index) in resource.topic_tags" :key="tag"
                        class="inline-flex items-center gap-1 bg-blue-100 dark:bg-blue-900/50 px-2 py-1 rounded-full text-xs font-medium text-blue-800 dark:text-blue-200">
                        <Icon v-if="index === 0" icon="mdi:bookmark" width="14" height="14" />
                        {{ tag }}
                    </span>
                </div>

                <!-- Programming Language Tags -->
                <div v-if="resource.programming_language_tags?.length" class="flex flex-wrap items-center gap-1">
                    <span v-for="(tag, index) in resource.programming_language_tags" :key="tag"
                        class="inline-flex items-center gap-1 bg-purple-100 dark:bg-purple-900/50 px-2 py-1 rounded-full text-xs font-medium text-purple-800 dark:text-purple-200">
                        <Icon v-if="index === 0" icon="mdi:language-typescript" width="14" height="14" />
                        {{ tag }}
                    </span>
                </div>

                <!-- General Tags -->
                <div v-if="resource.general_tags?.length" class="flex flex-wrap items-center gap-1">
                    <span v-for="(tag, index) in resource.general_tags" :key="tag"
                        class="inline-flex items-center gap-1 bg-accent/20 dark:bg-yellow-900/50 px-2 py-1 rounded-full text-xs font-medium text-yellow-800 dark:text-yellow-200">
                        <Icon v-if="index === 0" icon="mdi:tag" width="14" height="14" />
                        {{ tag }}
                    </span>
                </div>
            </div>
        </td>

        <!-- Rating column -->
        <td class="align-middle py-3 pr-3 whitespace-nowrap">
            <div class="flex flex-col items-center">
                <StarRating
                    :model-value="resource.review_summary?.overall_rating"
                    :size="20"
                    class="mb-1"
                />
                <div class="inline-flex items-center gap-0.5 text-xs font-medium text-gray-700 dark:text-gray-300">
                    <Icon icon="mdi:account-group" width="12" height="12" />
                    {{ resource.review_summary?.review_count || 0 }}
                </div>
            </div>
        </td>
    </tr>
</template>
