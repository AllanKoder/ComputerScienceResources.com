<script setup>
import { computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Icon } from "@iconify/vue";
import { diffChars } from "diff";
import { getDifficultyLabel, getPricingLabel, getPlatformLabel } from "@/Helpers/labels";

import AppLayout from "@/Layouts/AppLayout.vue";
import Tag from "primevue/tag";
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import ResourceThumbnail from "@/Components/Resources/ResourceThumbnail.vue";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import Commentable from "@/Components/Comments/Commentable.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import UserProfile from "@/Components/Profile/UserProfile.vue";
import BackButton from "@/Components/Navigation/BackButton.vue";

// Component mapping for the Difference (Diff) view
import TextDiffViewer from "@/Components/Diff/TextDiffViewer.vue";
import SelectDiffViewer from "@/Components/Diff/SelectDiffViewer.vue";
import TagDiffViewer from "@/Components/Diff/TagDiffViewer.vue";
import ImageDiffViewer from "@/Components/Diff/ImageDiffViewer.vue";



const props = defineProps({
    editedResource: {
        type: Object,
        required: true,
    },
});

const originalResource = props.editedResource.computer_science_resource;

// A map to get display labels, formatters, and diff components for each field.
const fieldConfig = {
    name: { label: "Name", component: TextDiffViewer },
    description: { label: "Description", component: TextDiffViewer },
    page_url: { label: "URL", component: TextDiffViewer },
    difficulties: {
        label: "Difficulty",
        formatter: getDifficultyLabel,
        component: TagDiffViewer,
    },
    pricing: {
        label: "Pricing",
        formatter: getPricingLabel,
        component: SelectDiffViewer,
    },
    image_url: { label: "Image", component: ImageDiffViewer },
    platforms: {
        label: "Platforms",
        component: TagDiffViewer,
        formatter: getPlatformLabel
    },
    topics_tags: { label: "Topic Tags", component: TagDiffViewer },
    programming_languages_tags: {
        label: "Programming Language Tags",
        component: TagDiffViewer,
    },
    general_tags: { label: "General Tags", component: TagDiffViewer },
};

// A simplified way to get all changed fields.
const changedFields = computed(() => {
    const changes = props.editedResource.proposed_changes || {};
    return Object.keys(changes)
        .map((key) => {
            const config = fieldConfig[key];
            if (config == undefined) return null; // Ignore keys not in config

            const proposedValue = changes[key];
            const originalValue = originalResource[key];

            // For text diffs, pre-calculate the diff array
            const diffArray =
                config.component === TextDiffViewer &&
                originalValue &&
                proposedValue
                    ? diffChars(originalValue || "", proposedValue || "")
                    : null;

            return {
                key,
                label: config.label,
                component: config.component,
                formatter: config.formatter,
                originalValue,
                proposedValue,
                diffArray,
            };
        })
        .filter(Boolean); // Filter out any nulls
});

function mergeEdits(id) {
    router.post(route("resource_edits.merge", { resourceEdits: id }));
}

const hasChanges = computed(() => changedFields.value.length > 0);
</script>

<template>
    <AppLayout :title="`Compare Versions: ${originalResource.name}`">
        <Head :title="`Edit for ${originalResource.name}`" />
        <div class="max-w-[90vw] mx-auto sm:px-6 py-4 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg border border-transparent dark:border-gray-800">
                <div class="p-7 sm:p-8">
                    <!-- Header Section -->
                    <div class="my-2">
                        <BackButton
                            :route="
                                route('resources.show', {
                                    slug: originalResource.slug,
                                    tab: 'edits',
                                })
                            "
                        />
                    </div>
                    <div class="border-b border-gray-200 dark:border-gray-800 pb-6 mb-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h1
                                        class="text-3xl font-bold text-gray-900 dark:text-gray-100"
                                    >
                                        {{ editedResource.edit_title }}
                                    </h1>
                                </div>
                                <p
                                    class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed mb-4"
                                >
                                    {{ editedResource.edit_description }}
                                </p>

                                <UserProfile
                                    :user="editedResource.user"
                                    :date="editedResource.created_at"
                                />
                            </div>
                            <div
                                v-if="editedResource.can_merge_edits"
                                class="ml-6"
                            >
                                <PrimaryButton
                                    @click="mergeEdits(editedResource.id)"
                                >
                                    <Icon
                                        icon="mdi:source-merge"
                                        class="w-4 h-4 mr-2"
                                    />
                                    Merge Changes
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>

                    <TabView class="custom-tabview">
                        <!-- Side-by-Side Comparison Tab -->
                        <TabPanel header="Split View" class="custom-tab-panel">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Proposed Changes Column -->
                                <div
                                    class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg"
                                >
                                    <div
                                        class="bg-primary/5 dark:bg-primary/10 border-b border-gray-200 dark:border-gray-800 px-4 py-3"
                                    >
                                        <h2
                                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2"
                                        >
                                            <Icon
                                                icon="mdi:plus-circle"
                                                class="w-5 h-5 text-primary"
                                            />
                                            Proposed Changes
                                        </h2>
                                    </div>
                                    <div class="p-4 space-y-4">
                                        <div
                                            v-for="field in changedFields"
                                            :key="field.key"
                                        >
                                            <h3
                                                class="font-semibold text-gray-600 dark:text-gray-300 mb-2"
                                            >
                                                {{ field.label }}:
                                            </h3>
                                            <div
                                                v-if="field.key === 'image_url'"
                                            >
                                                <p v-if="!field.proposedValue" class="italic">Image Removed</p>
                                                <ResourceThumbnail
                                                    :src="field.proposedValue"
                                                    :alt="'Proposed Image'"
                                                />
                                            </div>
                                            <div
                                                v-else-if="
                                                    Array.isArray(
                                                        field.proposedValue
                                                    )
                                                "
                                                class="flex flex-wrap gap-2"
                                            >
                                                <Tag
                                                    v-for="item in field.proposedValue"
                                                    :key="item"
                                                    :value="field.formatter ? field.formatter(item) : item"
                                                />
                                            </div>
                                            <div
                                                v-else
                                                class="text-gray-800 dark:text-gray-100 p-2 rounded"
                                            >
                                                {{
                                                    field.formatter
                                                        ? field.formatter(
                                                              field.proposedValue
                                                          )
                                                        : field.proposedValue
                                                }}
                                            </div>
                                        </div>
                                        <p
                                            v-if="!hasChanges"
                                            class="text-gray-500 dark:text-gray-400 italic"
                                        >
                                            No changes were proposed.
                                        </p>
                                    </div>
                                </div>

                                <!-- Current Version Column -->
                                <div
                                    class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg"
                                >
                                    <div
                                        class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800 px-4 py-3"
                                    >
                                        <Link
                                            :href="
                                                route('resources.show', {
                                                    slug: originalResource.slug,
                                                })
                                            "
                                            class="group"
                                        >
                                            <h2
                                                class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2 group-hover:text-primary duration-200"
                                            >
                                                <Icon
                                                    icon="mdi:file-document"
                                                    class="w-5 h-5 text-gray-600 dark:text-gray-300 group-hover:text-primary duration-200"
                                                />
                                                Current Version
                                            </h2>
                                        </Link>
                                    </div>
                                    <div class="p-4 space-y-4">
                                        <div
                                            v-for="field in changedFields"
                                            :key="field.key"
                                        >
                                            <h3
                                                class="font-semibold text-gray-600 dark:text-gray-300 mb-2"
                                            >
                                                {{ field.label }}:
                                            </h3>
                                            <div
                                                v-if="field.key === 'image_url'"
                                            >
                                                <p v-if="!field.originalValue" class="italic">Image Removed</p>
                                                <ResourceThumbnail
                                                    :src="field.originalValue"
                                                    :alt="'Current Image'"
                                                />
                                            </div>
                                            <div
                                                v-else-if="
                                                    Array.isArray(
                                                        field.originalValue
                                                    )
                                                "
                                                class="flex flex-wrap gap-2"
                                            >
                                                <Tag
                                                    v-for="item in field.originalValue"
                                                    :key="item"
                                                    :value="field.formatter ? field.formatter(item) : item"
                                                />
                                            </div>
                                            <div
                                                v-else
                                                class="text-gray-800 dark:text-gray-100 p-2 rounded"
                                            >
                                                {{
                                                    field.formatter
                                                        ? field.formatter(
                                                              field.originalValue
                                                          )
                                                        : field.originalValue
                                                }}
                                            </div>
                                        </div>
                                        <p
                                            v-if="!hasChanges"
                                            class="text-gray-500 dark:text-gray-400 italic"
                                        >
                                            No changes to compare.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </TabPanel>

                        <!-- Diff Tab -->
                        <TabPanel
                            header="View Differences"
                            class="custom-tab-panel"
                        >
                            <div v-if="hasChanges" class="space-y-6">
                                <div
                                    v-for="field in changedFields"
                                    :key="field.key"
                                    class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden"
                                >
                                    <div
                                        class="bg-gray-100 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-800"
                                    >
                                        <h3
                                            class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2"
                                        >
                                            <Icon
                                                icon="mdi:file-edit"
                                                class="w-4 h-4 text-primary"
                                            />
                                            {{ field.label }}
                                        </h3>
                                    </div>
                                    <div class="p-4">
                                        <component
                                            :is="field.component"
                                            :field="field"
                                        />
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-gray-500 dark:text-gray-400 italic p-4">
                                No changes to display in diff.
                            </p>
                        </TabPanel>
                    </TabView>
                    <!-- Approval Actions -->
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-800 pt-6">
                        <div class="flex justify-center">
                            <Upvotable
                                :flexRow="true"
                                :upvotable-key="'edit'"
                                :upvotable-id="editedResource.id"
                                :initial-votes="editedResource.vote_score"
                                :user-vote="editedResource.user_vote"
                                :refresh="true"
                                class="flex items-center gap-6"
                            >
                                <!-- Downvote (Reject) Button -->
                                <template #alreadyDownvotedIcon>
                                    <span
                                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg font-medium"
                                    >
                                        <Icon
                                            icon="mdi:close-circle"
                                            class="w-4 h-4 mr-2"
                                        />
                                        Rejected
                                    </span>
                                </template>
                                <template #downvoteIcon>
                                    <span
                                        class="inline-flex items-center px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg font-medium transition-colors cursor-pointer"
                                    >
                                        <Icon
                                            icon="mdi:close-circle-outline"
                                            class="w-4 h-4 mr-2"
                                        />
                                        Reject Changes
                                    </span>
                                </template>

                                <!-- Vote Count -->
                                <template #votes="{ votes }">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                                            >{{ votes }}</span
                                        >
                                        <span class="text-sm text-gray-600 dark:text-gray-300">
                                            Approval{{ votes === 1 ? "" : "s" }}
                                        </span>
                                    </div>
                                </template>

                                <!-- Upvote (Approve) Button -->
                                <template #alreadyUpvotedIcon>
                                    <span
                                        class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg font-medium"
                                    >
                                        <Icon
                                            icon="mdi:check-circle"
                                            class="w-4 h-4 mr-2"
                                        />
                                        Approved!
                                    </span>
                                </template>
                                <template #upvoteIcon>
                                    <span
                                        class="inline-flex items-center px-4 py-2 bg-green-200 hover:bg-green-300 text-green-800 border border-green-300 rounded-lg font-medium transition-colors cursor-pointer"
                                    >
                                        <Icon
                                            icon="mdi:check-circle-outline"
                                            class="w-4 h-4 mr-2"
                                        />
                                        Approve Changes
                                    </span>
                                </template>
                            </Upvotable>
                        </div>
                    </div>

                    <Commentable
                        :commentable-id="props.editedResource.id"
                        :commentable-key="'edit'"
                        :comments-count="props.editedResource.comments_count"
                    ></Commentable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
