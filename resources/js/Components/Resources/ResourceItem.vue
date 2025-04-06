<script setup>
import { Icon } from "@iconify/vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    resource: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["upvote", "downvote"]);

</script>

<template>
<tr class="mb-8 border-b p-12">
        <td class="align-top pr-6">
            <Upvotable
                :upvotable-id="resource.id"
                :upvotable-type="'resource'"
                :initial-votes="resource.vote_score"
                :user-vote="resource.user_vote"
            ></Upvotable>
        </td>

        <td class="align-top pr-6 w-32">
            <img
                :src="resource.image_url"
                :alt="resource.name"
                class="w-full h-auto object-contain rounded"
            />
        </td>

        <td class="align-top pr-6">
            <div class="flex justify-between items-start mb-3">
                <Link
                    :href="
                        route('resources.show', {
                            computerScienceResource: resource.id,
                        })
                    "
                >
                    <h2 class="text-xl font-semibold">{{ resource.name }}</h2>
                </Link>
                <time class="text-sm text-gray-500">{{
                    resource.resource_created_on
                }}</time>
            </div>
            <p class="text-gray-600 mb-4">{{ resource.description }}</p>
            <div class="flex flex-wrap gap-2">
                <span
                    v-for="type in resource.platforms"
                    :key="type"
                    class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded"
                >
                    {{ type.trim() }}
                </span>
                <span
                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded"
                >
                    {{ resource.pricing }}
                </span>

                <!-- Display topic tags -->
                <span
                    v-for="tag in resource.topic_tags"
                    :key="tag.id"
                    class="bg-gray-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded"
                >
                    {{ tag }}
                </span>

                <!-- Display programming language tags -->
                <span
                    v-for="tag in resource.programming_language_tags"
                    :key="tag.id"
                    class="bg-purple-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded"
                >
                    {{ tag }}
                </span>

                <!-- Display general tags -->
                <span
                    v-for="tag in resource.general_tags"
                    :key="tag.id"
                    class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded"
                >
                    {{ tag }}
                </span>
            </div>
        </td>

        <td class="align-top">
            <div class="flex flex-col items-center">
                <div class="text-2xl font-bold text-yellow-500 mb-2"></div>
                <div class="flex">
                    <Icon
                        v-for="i in (Math.floor(resource.review_summary?.average_reviews_score) || 0)"
                        :key="i"
                        icon="mdi:star"
                        class="text-yellow-500"
                        width="20"
                        height="20"
                    />
                </div>
                <div class="text-sm text-gray-500 mt-1">({{ resource.review_summary?.review_count || 0}} ratings)</div>
            </div>
        </td>
    </tr>
</template>
