<script setup>
import { computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { diffChars } from "diff";
import { getDifficultyLabel, getPricingLabel, getPlatformLabel } from "@/Helpers/labels";

import AppLayout from "@/Layouts/AppLayout.vue";
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from "primevue/tabpanel";
import Commentable from "@/Components/Comments/Commentable.vue";

// Component mapping for the Difference (Diff) view
import TextDiffViewer from "@/Components/Diff/TextDiffViewer.vue";
import SelectDiffViewer from "@/Components/Diff/SelectDiffViewer.vue";
import TagDiffViewer from "@/Components/Diff/TagDiffViewer.vue";
import ImageDiffViewer from "@/Components/Diff/ImageDiffViewer.vue";

// Resource Edit Components
import HeaderSection from "@/Components/ResourceEdits/HeaderSection.vue";
import SplitViewTab from "@/Components/ResourceEdits/SplitViewTab.vue";
import DiffViewTab from "@/Components/ResourceEdits/DiffViewTab.vue";
import ApprovalActions from "@/Components/ResourceEdits/ApprovalActions.vue";

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
                    <HeaderSection
                        :edited-resource="editedResource"
                        :original-resource="originalResource"
                        @merge="mergeEdits"
                    />

                    <Tabs value="0">
                        <TabList>
                            <Tab value="0">Split View</Tab>
                            <Tab value="1">View Differences</Tab>
                        </TabList>
                        <TabPanels>
                            <!-- Side-by-Side Comparison Tab -->
                            <TabPanel value="0">
                                <SplitViewTab
                                    :changed-fields="changedFields"
                                    :original-resource="originalResource"
                                    :has-changes="hasChanges"
                                />
                            </TabPanel>

                            <!-- Diff Tab -->
                            <TabPanel value="1">
                                <DiffViewTab
                                    :changed-fields="changedFields"
                                    :has-changes="hasChanges"
                                />
                            </TabPanel>
                        </TabPanels>
                    </Tabs>

                    <!-- Approval Actions -->
                    <ApprovalActions :edited-resource="editedResource" />

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
