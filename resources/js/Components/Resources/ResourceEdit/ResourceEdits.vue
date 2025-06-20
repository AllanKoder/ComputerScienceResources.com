<script setup>
import { Link } from "@inertiajs/vue3";
import { Icon } from "@iconify/vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import EmptyState from "@/Components/EmptyState.vue";

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
        <!-- Propose Edits Button -->
        <div class="flex justify-end mb-4">
            <Link
                :href="
                    route('resource_edits.create', {
                        computerScienceResource: props.resourceId,
                    })
                "
            >
                <PrimaryButton
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
                >
                    <Icon icon="mdi:pencil" class="w-5 h-5" />
                    <span>Propose Edits</span>
                </PrimaryButton>
            </Link>
        </div>

        <div class="space-y-6">
            <div
                v-for="edit in props.resourceEdits"
                :key="edit.id"
                class="bg-white/70 backdrop-blur-md p-6 rounded-lg shadow-md"
            >
                <div class="flex flex-row gap-4 items-start">
                    <Upvotable
                        :upvotable-key="'edit'"
                        :upvotable-id="edit.id"
                        :initial-votes="edit.vote_score"
                        :user-vote="edit.user_vote"
                    ></Upvotable>
                    <div class="flex-grow">
                        <Link
                            :href="
                                route('resource_edits.show', {
                                    resourceEdits: edit.id,
                                })
                            "
                        >
                            <h3 class="text-xl font-semibold">
                                {{ edit.edit_title }}
                            </h3>
                        </Link>
                        <p class="text-gray-700 mt-2">
                            {{ edit.edit_description }}
                        </p>
                    </div>
                </div>
            </div>
            <EmptyState
                v-if="props.resourceEdits.length === 0"
                icon="mdi:text-box-check-outline"
                title="No Proposed Edits Yet"
                message="Be the first to suggest a change!"
            />
        </div>
    </div>
</template>
