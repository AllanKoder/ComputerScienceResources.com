<script setup>
import Tabs from "@/Components/Resources/ResourceEdit/ResourceTab.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Icon } from "@iconify/vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";

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
    <AppLayout :title="`Edits for Resource ${resourceId}`">
        <Head :title="`Edits for Resource ${resourceId}`" />
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:p-8">
                        <!-- Use the Tabs component here -->
                        <Tabs :resource-id="resourceId" />

                        <!-- Display all resource edits -->
                        <div class="mt-6">
                            <!-- Propose Edits Button -->
                            <Link
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4"
                                :href="
                                    route('resource_edits.create', {
                                        computerScienceResource:
                                            props.resourceId,
                                    })
                                "
                            >
                                <Icon icon="mdi:pencil" class="w-5 h-5" />
                                <span>Propose Edits</span>
                            </Link>

                            <h2 class="text-2xl font-bold mb-4">
                                Proposed Edits
                            </h2>
                            <ul>
                                <li
                                    v-for="edit in props.resourceEdits"
                                    :key="edit.id"
                                    class="mb-4"
                                >
                                    <div class="flex flex-col bg-slate-100">
                                        <Upvotable
                                            :upvotable-type="'edit'"
                                            :upvotable-id="edit.id"
                                            :initial-votes="edit.vote_score"
                                            :user-vote="edit.user_vote"
                                        ></Upvotable>
                                        <div class="font-bold">Edit Title:</div>
                                        <Link
                                            :href="route('resource_edits.show', {'resourceEdits': edit.id})"
                                        >
                                            <div>{{ edit.edit_title }}</div>
                                        </Link>
                                        <div class="font-bold mt-2">
                                            Edit Description:
                                        </div>
                                        <div>{{ edit.edit_description }}</div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
