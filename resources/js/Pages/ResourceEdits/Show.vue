<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { computed, ref } from "vue";
import * as Diff from "diff";
import Tag from "primevue/tag";
import Button from "primevue/button";
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import { pricingLabels, difficultyLabels } from "@/Helpers/constants.js";

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

const platformColors = {
    book: "blue",
    podcast: "green",
    youtube_channel: "red",
    blog: "orange",
    website: "purple",
    organization: "cyan",
    bootcamp: "pink",
    newsletter: "indigo",
    workshop: "teal",
    course: "yellow",
    forum: "gray",
    mobile_app: "lime",
    desktop_app: "amber",
    magazine: "rose",
};

// Helper function to parse tags safely
const parseTags = (tags) => {
    if (Array.isArray(tags)) return tags;

    if (typeof tags === "string") {
        try {
            return JSON.parse(tags);
        } catch {
            return tags
                .replace(/[\[\]"]/g, "")
                .split(",")
                .map((tag) => tag.trim());
        }
    }

    return [];
};

const originalPlatformList = computed(() =>
    props.originalResource.platforms.split(",").map((platform) => platform.trim())
);

const editedPlatformList = computed(() =>
    props.editedResource.platforms
        .split(",")
        .map((platform) => platform.trim())
);

const originalTopicTags = computed(() =>
    parseTags(props.originalResource.topic_tags)
);
const originalProgrammingTags = computed(() =>
    parseTags(props.originalResource.programming_language_tags)
);
const originalGeneralTags = computed(() =>
    parseTags(props.originalResource.general_tags)
);

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
        originalTopicTags.value,
        props.editedResource.topic_tags || []
    ),
    programming_language_tags: Diff.diffArrays(
        originalProgrammingTags.value,
        props.editedResource.programming_language_tags || []
    ),
    general_tags: Diff.diffArrays(
        originalGeneralTags.value,
        props.editedResource.general_tags || []
    ),
}));

// Compute diffs for platforms
const platformDiffs = computed(() =>
    Diff.diffArrays(originalPlatformList.value, editedPlatformList.value)
);

const handleApprove = () => {
    emit("approveChanges", props.resourceId);
};

const handleReject = () => {
    emit("rejectChanges", props.resourceId);
};

// Helper to render diff spans
const renderDiffSpan = (part) => {
    if (part.added) return `<span class="bg-green-100">${part.value}</span>`;
    if (part.removed) return `<span class="bg-red-100">${part.value}</span>`;
    return part.value;
};
</script>

<template>
    <AppLayout :title="`Compare Versions: ${props.originalResource.name}`">
        <Head :title="`Compare Versions: ${props.originalResource.name}`" />

        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <h1 class="text-2xl font-bold mb-6">
                        {{ editedResource.edit_title }}
                    </h1>
                    <span>Reasoning for Edit: </span>
                    <p>{{ editedResource.edit_description }}</p>
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
                                                'bg-yellow-50':
                                                    props.originalResource[
                                                        field.key
                                                    ] !==
                                                    props.editedResource[
                                                        field.key
                                                    ],
                                                'p-1 rounded':
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
                                                          props
                                                              .editedResource[
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
                                                v-for="platform in editedPlatformList"
                                                :key="platform"
                                                :value="platform"
                                                :severity="
                                                    platformColors[platform]
                                                "
                                                class="capitalize"
                                                :class="{
                                                    'bg-yellow-50':
                                                        !originalPlatformList.includes(
                                                            platform
                                                        ),
                                                }"
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
                                                :class="{
                                                    'bg-yellow-50':
                                                        !originalTopicTags.includes(
                                                            tag
                                                        ),
                                                }"
                                            />
                                            <Tag
                                                v-for="tag in props
                                                    .editedResource
                                                    .programming_language_tags"
                                                :key="tag"
                                                :value="tag"
                                                severity="success"
                                                class="text-xs mr-1 mb-1"
                                                :class="{
                                                    'bg-yellow-50':
                                                        !originalProgrammingTags.includes(
                                                            tag
                                                        ),
                                                }"
                                            />
                                            <Tag
                                                v-for="tag in props
                                                    .editedResource
                                                    .general_tags"
                                                :key="tag"
                                                :value="tag"
                                                severity="warning"
                                                class="text-xs mr-1 mb-1"
                                                :class="{
                                                    'bg-yellow-50':
                                                        !originalGeneralTags.includes(
                                                            tag
                                                        ),
                                                }"
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
                                                          props.originalResource[
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
                                                v-for="platform in originalPlatformList"
                                                :key="platform"
                                                :value="platform"
                                                :severity="
                                                    platformColors[platform]
                                                "
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
                                                v-for="tag in originalTopicTags"
                                                :key="tag"
                                                :value="tag"
                                                severity="info"
                                                class="text-xs mr-1 mb-1"
                                            />
                                            <Tag
                                                v-for="tag in originalProgrammingTags"
                                                :key="tag"
                                                :value="tag"
                                                severity="success"
                                                class="text-xs mr-1 mb-1"
                                            />
                                            <Tag
                                                v-for="tag in originalGeneralTags"
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
                            <div class="space-y-6">
                                <!-- Text Field Diffs -->
                                <div
                                    v-for="field in compareFields.filter(
                                        (f) => f.type !== 'select'
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

                                <!-- Platform Diffs -->
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold mb-2">
                                        Platforms Diff:
                                    </h3>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div
                                            v-for="part in platformDiffs"
                                            :key="part.value"
                                            :class="{
                                                'bg-green-100': part.added,
                                                'bg-red-100': part.removed,
                                                'p-1 rounded':
                                                    part.added || part.removed,
                                            }"
                                        >
                                            {{ part.value.join(", ") }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Tag Diffs -->
                                <div class="space-y-4">
                                    <div
                                        v-for="(diff, tagType) in tagDiffs"
                                        :key="tagType"
                                    >
                                        <h3 class="text-lg font-semibold mb-2">
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
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <div
                                                v-for="part in diff"
                                                :key="part.value"
                                                :class="{
                                                    'bg-green-100': part.added,
                                                    'bg-red-100': part.removed,
                                                    'p-1 rounded':
                                                        part.added ||
                                                        part.removed,
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
                                </div>
                            </div>
                        </TabPanel>
                    </TabView>

                    <!-- Approval Actions -->
                    <div class="mt-6 flex justify-end space-x-4">
                        <Button
                            label="Reject Changes"
                            severity="danger"
                            @click="handleReject"
                        />
                        <Button
                            label="Approve Changes"
                            severity="success"
                            @click="handleApprove"
                        />
                    </div>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
