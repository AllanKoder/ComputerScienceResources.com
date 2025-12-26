<script setup>
import { Link } from "@inertiajs/vue3";
import ClickableHeading from "@/Components/ClickableHeading.vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import EmptyState from "@/Components/EmptyState.vue";
import PaginateLinks from "@/Components/Pagination/PaginateLinks.vue";
import UserProfile from "@/Components/Profile/UserProfile.vue";

const props = defineProps({
    resourceEdits: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4">
        <div class="overflow-x-auto">
            <div class="w-full flex flex-col gap-2">
                <div
                    v-for="(edit, idx) in props.resourceEdits.data"
                    :key="edit.id"
                    class="bg-white/70 dark:bg-gray-900/90 backdrop-blur-md p-6 rounded-sm shadow-md border dark:border-gray-800"
                >
                    <div class="flex flex-row gap-4 items-center">
                        <Upvotable
                            :upvotable-key="'edit'"
                            :upvotable-id="edit.id"
                            :initial-votes="edit.vote_score"
                            :user-vote="edit.user_vote"
                        ></Upvotable>
                        <div class="flex-grow align-middle items-center">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                <div class="flex-grow">
                                    <ClickableHeading :href="route('resource_edits.show', { slug: edit.slug })">
                                        <h2
                                            class="text-lg font-semibold tracking-tight text-gray-900 dark:text-gray-100 group-hover:text-primary transition-colors duration-200 font-sans"
                                        >
                                            {{ edit.edit_title }}
                                        </h2>
                                    </ClickableHeading>

                                    <!-- Show resource name if available -->
                                    <div v-if="edit.computer_science_resource" class="mt-1">
                                        <Link
                                            :href="route('resources.show', { slug: edit.computer_science_resource.slug })"
                                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primaryLight transition-colors"
                                        >
                                            Edit for: {{ edit.computer_science_resource.name }}
                                        </Link>
                                    </div>
                                </div>

                                <UserProfile
                                    :user="edit.user"
                                    :date="edit.created_at"
                                    class="sm:ml-4 flex-shrink-0"
                                />
                            </div>

                            <p class="text-gray-700 dark:text-gray-300 mt-2">
                                {{ edit.edit_description }}
                            </p>
                        </div>
                    </div>
                    <div v-if="idx < props.resourceEdits.data.length - 1" class="border-t border-gray-200 dark:border-gray-700 my-2 mt-4"></div>
                </div>
            </div>

            <!-- Empty State -->
            <EmptyState
                v-if="props.resourceEdits.data.length === 0"
                icon="mdi:text-box-check-outline"
                title="No Proposed Edits Yet"
                message="Be the first to suggest a change!"
            />

            <!-- Pagination Links -->
            <div v-if="props.resourceEdits.data.length > 0" class="mb-4 mt-6">
                <PaginateLinks
                    :model-name="'edits'"
                    :links="props.resourceEdits.links"
                    :from="props.resourceEdits.from"
                    :to="props.resourceEdits.to"
                    :total="props.resourceEdits.total"
                ></PaginateLinks>
            </div>
        </div>
    </div>
</template>
