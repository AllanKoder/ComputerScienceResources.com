<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { computed, ref } from "vue";
import * as Diff from "diff";
import Tag from "primevue/tag";
import Button from "primevue/button";
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import {
    pricingLabels,
    difficultyLabels,
} from "@/Helpers/labels.js";
import Upvotable from "@/Components/Upvote/Upvotable.vue";
import Commentable from "@/Components/Comments/Commentable.vue";

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

// TODO: FIx the diff, so it actually shows the diff

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
    if (part.added) return `<span class="bg-green-100">${part.value}</span>`;
    if (part.removed) return `<span class="bg-red-100">${part.value}</span>`;
    return part.value;
};

function mergeEdits(id) {
    router.post(route("resource_edits.merge", { resourceEdits: id }));
}
</script>

<template>
    <AppLayout :title="`Compare Versions: ${props.originalResource.name}`">
        <Head :title="`Compare Versions: ${props.originalResource.name}`" />
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <!-- Viewing title and decription-->
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl font-bold mb-2">
                                {{ editedResource.edit_title }}
                            </h1>
                            <p>{{ editedResource.edit_description }}</p>
                        </div>
                        <div v-if="editedResource.can_merge_edits">
                            <!-- Merge button -->
                            <button
                                @click="mergeEdits(editedResource.id)"
                                class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition"
                            >
                                Merge
                            </button>
                        </div>
                    </div>

                    <TabView>
                        <!-- Side-by-Side Comparison Tab -->
                        <TabPanel header="Side-by-Side View">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Edited Version -->
                                <div class="border rounded-lg p-4">
                                    <h2
                                        class="text-xl font-semibold mb-4 text-gray-700"
                                    >
                                        Edited Version
                                    </h2>

                                    <!-- Comparison Fields -->
                                    <div
                                        v-for="field in compareFields"
                                        :key="field.key"
                                        class="mb-4"
                                    >
                                        <div
                                            class="font-semibold text-gray-600"
                                        >
                                            {{ field.label }}:
                                        </div>
                                        <div
                                            class="text-gray-800"
                                            :class="{
                                                'bg-yellow-50 p-1 rounded':
                                                    props.originalResource[
                                                        field.key
                                                    ] !==
                                                    props.editedResource[
                                                        field.key
                                                    ],
                                            }"
                                        >
                                            {{
                                                field.formatter
                                                    ? field.formatter(
                                                          props.editedResource[
                                                              field.key
                                                          ]
                                                      )
                                                    : props.editedResource[
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
                                                    .editedResource.platforms"
                                                :key="platform"
                                                :value="platform"
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
                                                    .editedResource.topic_tags"
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
                                <!-- Original Version -->
                                <div class="border rounded-lg p-4">
                                    <h2
                                        class="text-xl font-semibold mb-4 text-gray-700"
                                    >
                                        Original Version
                                    </h2>

                                    <!-- Comparison Fields -->
                                    <div
                                        v-for="field in compareFields"
                                        :key="field.key"
                                        class="mb-4"
                                    >
                                        <div
                                            class="font-semibold text-gray-600"
                                        >
                                            {{ field.label }}:
                                        </div>
                                        <div class="text-gray-800">
                                            {{
                                                field.formatter
                                                    ? field.formatter(
                                                          props
                                                              .originalResource[
                                                              field.key
                                                          ]
                                                      )
                                                    : props.originalResource[
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
                                                    .originalResource.platforms"
                                                :key="platform"
                                                :value="platform"
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
                        </TabPanel>

                        <!-- Diff View Tab -->
                        <TabPanel header="Detailed Diff">
                            <!-- Only display diff sections if there are changes -->
                            <div
                                v-if="
                                    hasTextDiffs ||
                                    hasPlatformDiff ||
                                    hasTagDiffs
                                "
                                class="space-y-6"
                            >
                                <!-- Text Field Diffs -->
                                <div v-if="hasTextDiffs">
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
                                        class="mb-4"
                                    >
                                        <h3 class="text-lg font-semibold mb-2">
                                            {{ field.label }} Diff:
                                        </h3>
                                        <div
                                            class="bg-gray-50 p-3 rounded-lg font-mono text-sm"
                                            v-html="
                                                textDiffs[field.key]
                                                    .map(renderDiffSpan)
                                                    .join('')
                                            "
                                        ></div>
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
                    <div class="mt-8 flex justify-center">
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
                                    class="bg-red-700 text-white px-3 py-1 rounded-full text-sm"
                                >
                                    Rejected
                                </span>
                            </template>
                            <template #downvoteIcon>
                                <span
                                    class="bg-red-100 text-red-900 px-3 py-1 rounded-full text-sm"
                                >
                                    Reject Changes
                                </span>
                            </template>

                            <!-- Vote Count -->
                            <template #votes="{ votes }">
                                <Tag severity="secondary" rounded>
                                    {{ votes }} Approval{{
                                        votes === 1 ? "" : "s"
                                    }}
                                </Tag>
                            </template>

                            <!-- Upvote (Approve) Button -->
                            <template #alreadyUpvotedIcon>
                                <span
                                    class="bg-green-600 text-white px-3 py-1 rounded-full text-sm"
                                >
                                    Approved!
                                </span>
                            </template>
                            <template #upvoteIcon>
                                <span
                                    class="bg-green-100 text-green-900 px-3 py-1 rounded-full text-sm"
                                >
                                    Approve Changes
                                </span>
                            </template>
                        </Upvotable>
                    </div>

                    <Commentable
                        :commentable-id="props.editedResource.id"
                        :commentable-key="'edit'"
                        :comments-count="props.editedResource.comments_count"
                    ></Commentable>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
