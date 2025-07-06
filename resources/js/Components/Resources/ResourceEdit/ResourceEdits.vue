<script setup>
import { Link } from "@inertiajs/vue3";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import EmptyState from "@/Components/EmptyState.vue";
import PaginateLinks from "@/Components/Pagination/PaginateLinks.vue";
import UserProfile from "@/Components/Profile/UserProfile.vue";

const props = defineProps({
    resourceId: {
        type: Number,
        required: true,
    },
    resourceEdits: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="px-6 max-w-7xl mx-auto mb-2">
        <div class="space-y-6">
            <div
                v-for="edit in props.resourceEdits.data"
                :key="edit.id"
                class="bg-white/70 backdrop-blur-md p-6 rounded-lg shadow-md"
            >
                <div class="flex flex-row gap-4 items-center">
                    <Upvotable
                        :upvotable-key="'edit'"
                        :upvotable-id="edit.id"
                        :initial-votes="edit.vote_score"
                        :user-vote="edit.user_vote"
                    ></Upvotable>
                    <div class="flex-grow align-middle items-center">
                        <Link
                            :href="
                                route('resource_edits.show', {
                                    resourceEdits: edit.id,
                                })
                                "
                        >
                            <h2
                                class="text-lg font-semibold tracking-tight text-gray-900 dark:text-gray-100 hover:text-primary transition-colors duration-200 font-sans"
                                >
                                {{ edit.edit_title }}
                            </h2>
                        </Link>

                        <UserProfile
                            :user="edit.user"
                            :date="edit.created_at"
                            class="mt-2"
                        />

                        <p class="text-gray-700 mt-2">
                            {{ edit.edit_description }}
                        </p>
                    </div>
                </div>
            </div>
            <PaginateLinks
                v-if="props.resourceEdits.data.length > 0"
                class="mt-6"
                :modelName="'edits'"
                :links="props.resourceEdits.links"
                :from="props.resourceEdits.from"
                :to="props.resourceEdits.to"
                :total="props.resourceEdits.total"
            ></PaginateLinks>
            <EmptyState
                v-else
                icon="mdi:text-box-check-outline"
                title="No Proposed Edits Yet"
                message="Be the first to suggest a change!"
            />
        </div>
    </div>
</template>
