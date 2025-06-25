<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { computed, ref } from "vue";
import * as Diff from "diff";
import Tag from "primevue/tag";
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import { pricingLabels, difficultyLabels } from "@/Helpers/labels.js";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import { Icon } from "@iconify/vue";
import Commentable from "@/Components/Comments/Commentable.vue";
import UserProfile from "@/Components/Profile/UserProfile.vue";

const props = defineProps({
    originalResource: {
        type: Object,
        required: true,
    },
    editedResource: {
        type: Object,
        required: true,
    },
    resourceId: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(["approveChanges", "rejectChanges"]);

const compareFields = [
    {
        key: "name",
        label: "Name",
        type: "text",
    },
    {
        key: "description",
        label: "Description",
        type: "textarea",
    },
    {
        key: "page_url",
        label: "Resource URL",
        type: "url",
    },
    {
        key: "difficulty",
        label: "Difficulty",
        type: "select",
        formatter: (value) => difficultyLabels[value],
    },
    {
        key: "pricing",
        label: "Pricing",
        type: "select",
        formatter: (value) => pricingLabels[value],
    },
];

// Compute diffs for text fields
const textDiffs = computed(() => {
    return compareFields.reduce((diffs, field) => {
        if (field.type !== "select") {
            diffs[field.key] = Diff.diffChars(
                props.originalResource[field.key] || "",
                props.editedResource[field.key] || ""
            );
        }
        return diffs;
    }, {});
});

// Compute diffs for tags
const tagDiffs = computed(() => ({
    topic_tags: Diff.diffArrays(
        props.originalResource.topic_tags || [],
        props.editedResource.topic_tags || []
    ),
    programming_language_tags: Diff.diffArrays(
        props.originalResource.programming_language_tags || [],
        props.editedResource.programming_language_tags || []
    ),
    general_tags: Diff.diffArrays(
        props.originalResource.general_tags || [],
        props.editedResource.general_tags || []
    ),
}));

// Compute diffs for platforms
const platformDiffs = computed(() =>
    Diff.diffArrays(
        props.originalResource.platforms,
        props.editedResource.platforms
    )
);

// Computed flags to check if there are differences
const hasTextDiffs = computed(
    () =>
        compareFields.filter(
            (f) =>
                f.type !== "select" &&
                props.editedResource[f.key] !== props.originalResource[f.key]
        ).length > 0
);

const hasSelectDiffs = computed(
    () =>
        compareFields.filter(
            (f) =>
                f.type === "select" &&
                props.editedResource[f.key] !== props.originalResource[f.key]
        ).length > 0
);

const hasPlatformDiff = computed(
    () =>
        JSON.stringify(props.originalResource.platforms) !==
        JSON.stringify(props.editedResource.platforms)
);

const tagDiffKeys = ["topic_tags", "programming_language_tags", "general_tags"];
const hasTagDiffs = computed(() => {
    return tagDiffKeys.some((tagType) => {
        const original = props.originalResource[tagType] || [];
        const edited = props.editedResource[tagType] || [];
        return JSON.stringify(original) !== JSON.stringify(edited);
    });
});

// Helper to render diff spans
const renderDiffSpan = (part) => {
    if (part.added)
        return `<span class="bg-green-50 text-green-800 border-l-4 border-green-500 pl-2 pr-1">${part.value}</span>`;
    if (part.removed)
        return `<span class="bg-red-50 text-red-800 border-l-4 border-red-500 pl-2 pr-1">${part.value}</span>`;
    return part.value;
};

function mergeEdits(id) {
    router.post(route("resource_edits.merge", { resourceEdits: id }));
}
</script>

<template>
    <AppLayout :title="`Compare Versions: ${props.originalResource.name}`">
        <Head :title="`Compare Versions: ${props.originalResource.name}`" />
        <div class="max-w-[90vw] mx-auto sm:px-6 py-4 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-7 sm:p-8">
                    <!-- Header Section -->
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h1
                                        class="text-3xl font-bold text-gray-900"
                                    >
                                        {{ editedResource.edit_title }}
                                    </h1>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-secondary text-primary border border-primary/20"
                                    >
                                        Proposed Edit
                                    </span>
                                </div>
                                <p
                                    class="text-gray-700 text-lg leading-relaxed mb-4"
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
                                <button
                                    @click="mergeEdits(editedResource.id)"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm"
                                >
                                    <Icon
                                        icon="mdi:source-merge"
                                        class="w-4 h-4 mr-2"
                                    />
                                    Merge Changes
                                </button>
                            </div>
                        </div>
                    </div>

                    <TabView class="custom-tabview">
                        <!-- Side-by-Side Comparison Tab -->
                        <TabPanel header="Split View" class="custom-tab-panel">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Edited Version -->
                                <div
                                    class="bg-gray-50 border border-gray-200 rounded-lg"
                                >
                                    <div
                                        class="bg-primary/5 border-b border-gray-200 px-4 py-3"
                                    >
                                        <h2
                                            class="text-lg font-semibold text-gray-900 flex items-center gap-2"
                                        >
                                            <Icon
                                                icon="mdi:plus-circle"
                                                class="w-5 h-5 text-green-600"
                                            />
                                            Proposed Changes
                                        </h2>
                                    </div>
                                    <div class="p-4">
                                        <!-- Comparison Fields -->
                                        <div
                                            v-for="field in compareFields"
                                            :key="field.key"
                                            class="mb-4"
                                        >
                                            <div v-if="props.editedResource[field.key] != props.originalResource[field.key]">
                                                <div
                                                    class="font-semibold text-gray-600 mb-2"
                                                >
                                                    {{ field.label }}:
                                                </div>

                                                <div
                                                    class="text-gray-800 p-2 rounded"
                                                >
                                                    {{
                                                        field.formatter
                                                            ? field.formatter(
                                                                  props
                                                                      .editedResource[
                                                                      field.key
                                                                  ]
                                                              )
                                                            : props
                                                                  .editedResource[
                                                                  field.key
                                                              ]
                                                    }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Platforms -->
                                        <div class="mb-4">
                                            <div
                                                class="font-semibold text-gray-600 mb-2"
                                            >
                                                Platforms:
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <Tag
                                                    v-for="platform in props
                                                        .editedResource
                                                        .platforms"
                                                    :key="platform"
                                                    :value="platform"
                                                    severity="warning"
                                                    class="capitalize"
                                                />
                                            </div>
                                        </div>

                                        <!-- Tags -->
                                        <div class="mb-4">
                                            <div
                                                class="font-semibold text-gray-600 mb-2"
                                            >
                                                Tags:
                                            </div>
                                            <div class="flex flex-wrap gap-1">
                                                <Tag
                                                    v-for="tag in props
                                                        .editedResource
                                                        .topic_tags"
                                                    :key="tag"
                                                    :value="tag"
                                                    severity="info"
                                                    class="text-xs mr-1 mb-1"
                                                />
                                                <Tag
                                                    v-for="tag in props
                                                        .editedResource
                                                        .programming_language_tags"
                                                    :key="tag"
                                                    :value="tag"
                                                    severity="success"
                                                    class="text-xs mr-1 mb-1"
                                                />
                                                <Tag
                                                    v-for="tag in props
                                                        .editedResource
                                                        .general_tags"
                                                    :key="tag"
                                                    :value="tag"
                                                    severity="warning"
                                                    class="text-xs mr-1 mb-1"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Original Version -->
                                <div
                                    class="bg-gray-50 border border-gray-200 rounded-lg"
                                >
                                    <div
                                        class="bg-gray-100 border-b border-gray-200 px-4 py-3"
                                    >
                                        <Link
                                            :href="
                                                route('resources.show', {
                                                    computerScienceResource:
                                                        props.originalResource
                                                            .id,
                                                })
                                            "
                                            class="group"
                                        >
                                            <h2
                                                class="text-lg font-semibold text-gray-900 flex items-center gap-2 group-hover:text-primary duration-200"
                                            >
                                                <Icon
                                                    icon="mdi:file-document"
                                                    class="w-5 h-5 text-gray-600 group-hover:text-primary duration-200"
                                                />
                                                Current Version
                                            </h2>
                                        </Link>
                                    </div>
                                    <div class="p-4">
                                        <!-- Comparison Fields -->
                                        <div
                                            v-for="field in compareFields"
                                            :key="field.key"
                                            class="mb-4"
                                        >
                                            <div
                                                class="font-semibold text-gray-600 mb-2"
                                            >
                                                {{ field.label }}:
                                            </div>
                                            <div
                                                class="text-gray-800 p-2 rounded"
                                            >
                                                {{
                                                    field.formatter
                                                        ? field.formatter(
                                                              props
                                                                  .originalResource[
                                                                  field.key
                                                              ]
                                                          )
                                                        : props
                                                              .originalResource[
                                                              field.key
                                                          ]
                                                }}
                                            </div>
                                        </div>

                                        <!-- Platforms -->
                                        <div class="mb-4">
                                            <div
                                                class="font-semibold text-gray-600 mb-2"
                                            >
                                                Platforms:
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <Tag
                                                    v-for="platform in props
                                                        .originalResource
                                                        .platforms"
                                                    :key="platform"
                                                    :value="platform"
                                                    severity="secondary"
                                                    class="capitalize"
                                                />
                                            </div>
                                        </div>

                                        <!-- Tags -->
                                        <div class="mb-4">
                                            <div
                                                class="font-semibold text-gray-600 mb-2"
                                            >
                                                Tags:
                                            </div>
                                            <div class="flex flex-wrap gap-1">
                                                <Tag
                                                    v-for="tag in props
                                                        .originalResource
                                                        .topic_tags"
                                                    :key="tag"
                                                    :value="tag"
                                                    severity="info"
                                                    class="text-xs mr-1 mb-1"
                                                />
                                                <Tag
                                                    v-for="tag in props
                                                        .originalResource
                                                        .programming_language_tags"
                                                    :key="tag"
                                                    :value="tag"
                                                    severity="success"
                                                    class="text-xs mr-1 mb-1"
                                                />
                                                <Tag
                                                    v-for="tag in props
                                                        .originalResource
                                                        .general_tags"
                                                    :key="tag"
                                                    :value="tag"
                                                    severity="warning"
                                                    class="text-xs mr-1 mb-1"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </TabPanel>

                        <!-- Diff View Tab -->
                        <TabPanel
                            header="Unified Diff"
                            class="custom-tab-panel"
                        >
                            <!-- Only display diff sections if there are changes -->
                            <div
                                v-if="
                                    hasTextDiffs ||
                                    hasSelectDiffs ||
                                    hasPlatformDiff ||
                                    hasTagDiffs
                                "
                                class="space-y-6"
                            >
                                <!-- Text Field Diffs -->
                                <div v-if="hasTextDiffs" class="space-y-4">
                                    <div
                                        v-for="field in compareFields.filter(
                                            (f) =>
                                                f.type !== 'select' &&
                                                props.editedResource[f.key] !==
                                                    props.originalResource[
                                                        f.key
                                                    ]
                                        )"
                                        :key="field.key"
                                        class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden"
                                    >
                                        <div
                                            class="bg-gray-100 px-4 py-2 border-b border-gray-200"
                                        >
                                            <h3
                                                class="font-semibold text-gray-900 flex items-center gap-2"
                                            >
                                                <Icon
                                                    icon="mdi:file-edit"
                                                    class="w-4 h-4 text-primary"
                                                />
                                                {{ field.label }}
                                            </h3>
                                        </div>
                                        <div
                                            class="p-4 font-mono text-sm leading-relaxed"
                                            v-html="
                                                textDiffs[field.key]
                                                    .map(renderDiffSpan)
                                                    .join('')
                                            "
                                        ></div>
                                    </div>
                                </div>

                                <!-- Select Field Diffs -->
                                <div v-if="hasSelectDiffs" class="space-y-4">
                                    <div
                                        v-for="field in compareFields.filter(
                                            (f) =>
                                                f.type === 'select' &&
                                                props.editedResource[f.key] !==
                                                    props.originalResource[
                                                        f.key
                                                    ]
                                        )"
                                        :key="field.key"
                                        class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden"
                                    >
                                        <div
                                            class="bg-gray-100 px-4 py-2 border-b border-gray-200"
                                        >
                                            <h3
                                                class="font-semibold text-gray-900 flex items-center gap-2"
                                            >
                                                <Icon
                                                    icon="mdi:swap-horizontal"
                                                    class="w-4 h-4 text-primary"
                                                />
                                                {{ field.label }}
                                            </h3>
                                        </div>
                                        <div class="p-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-600 mb-1">From:</div>
                                                    <span class="bg-red-50 text-red-800 border-l-4 border-red-500 pl-2 pr-1 py-1 rounded">
                                                        {{ field.formatter ? field.formatter(props.originalResource[field.key]) : props.originalResource[field.key] }}
                                                    </span>
                                                </div>
                                                <Icon icon="mdi:arrow-right" class="w-5 h-5 text-gray-400" />
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-600 mb-1">To:</div>
                                                    <span class="bg-green-50 text-green-800 border-l-4 border-green-500 pl-2 pr-1 py-1 rounded">
                                                        {{ field.formatter ? field.formatter(props.editedResource[field.key]) : props.editedResource[field.key] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Platform Diffs -->
                                <div v-if="hasPlatformDiff" class="mb-4">
                                    <h3 class="text-lg font-semibold mb-2">
                                        Platforms Diff:
                                    </h3>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div
                                            v-for="(
                                                part, index
                                            ) in platformDiffs"
                                            :key="index"
                                            :class="{
                                                'bg-green-100': part.added,
                                                'bg-red-100': part.removed,
                                                'p-1 rounded':
                                                    part.added || part.removed,
                                            }"
                                        >
                                            {{
                                                Array.isArray(part.value)
                                                    ? part.value.join(", ")
                                                    : part.value
                                            }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Tag Diffs -->
                                <div v-if="hasTagDiffs" class="space-y-4">
                                    <div
                                        v-for="(diff, tagType) in tagDiffs"
                                        :key="tagType"
                                    >
                                        <template
                                            v-if="
                                                JSON.stringify(
                                                    props.editedResource[
                                                        tagType
                                                    ]
                                                ) !==
                                                JSON.stringify(
                                                    props.originalResource[
                                                        tagType
                                                    ]
                                                )
                                            "
                                        >
                                            <h3
                                                class="text-lg font-semibold mb-2"
                                            >
                                                {{
                                                    tagType === "topic_tags"
                                                        ? "Topic"
                                                        : tagType ===
                                                          "programming_language_tags"
                                                        ? "Programming Language"
                                                        : "General"
                                                }}
                                                Tags Diff:
                                            </h3>
                                            <div
                                                class="bg-gray-50 p-3 rounded-lg"
                                            >
                                                <div
                                                    v-for="(
                                                        part, index
                                                    ) in diff"
                                                    :key="index"
                                                    :class="{
                                                        'bg-green-100':
                                                            part.added,
                                                        'bg-red-100':
                                                            part.removed,
                                                        'p-1 rounded':
                                                            part.added ||
                                                            part.removed,
                                                    }"
                                                >
                                                    {{
                                                        Array.isArray(
                                                            part.value
                                                        )
                                                            ? part.value.join(
                                                                  ", "
                                                              )
                                                            : part.value
                                                    }}
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <!-- If no diff exists, show a friendly message -->
                            <div v-else class="p-4 text-gray-500">
                                No differences found.
                            </div>
                        </TabPanel>
                    </TabView>

                    <!-- Approval Actions -->
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <div class="flex justify-center">
                            <!-- TODO: Add partial reload to the refresh -->
                            <Upvotable
                                :flexRow="true"
                                :upvotable-key="'edit'"
                                :upvotable-id="resourceId"
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
                                            class="text-2xl font-bold text-gray-900"
                                            >{{ votes }}</span
                                        >
                                        <span class="text-sm text-gray-600">
                                            Approval{{ votes === 1 ? "" : "s" }}
                                        </span>
                                    </div>
                                </template>

                                <!-- Upvote (Approve) Button -->
                                <template #alreadyUpvotedIcon>
                                    <span
                                        class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-medium"
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
                                        class="inline-flex items-center px-4 py-2 bg-secondary hover:bg-secondaryDark text-primary border border-primary/20 rounded-lg font-medium transition-colors cursor-pointer"
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
