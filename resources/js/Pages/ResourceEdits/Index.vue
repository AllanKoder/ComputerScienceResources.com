<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import { Head } from '@inertiajs/vue3';
import UpvoteSorting from "@/Components/Upvote/UpvoteSorting.vue";
import ResourceEditsTable from "@/Components/Resources/ResourceEdit/ResourceEditsTable.vue";
import ResourceEditsFAQ from "@/Components/Resources/ResourceEdit/ResourceEditsFAQ.vue";

const props = defineProps({
    resource_edits: {
        type: Object,
        required: true,
    },
    sortingType: {
        type: String,
        default: 'top',
    },
});

</script>

<template>
    <AppLayout title="Resource Edits">
        <!-- Page metadata -->
        <Head>
            <meta head-key="resource-edits:description" name="description" content="Browse and vote on proposed edits to computer science resources. Help improve our community-curated database!" />
            <meta head-key="resource-edits:og:title" property="og:title" content="Resource Edits - Computer Science Resources" />
            <meta head-key="resource-edits:og:description" property="og:description" content="Browse and vote on proposed edits to computer science resources. Help improve our community-curated database!" />
            <meta head-key="resource-edits:og:type" property="og:type" content="website" />
            <meta head-key="resource-edits:og:image" property="og:image" content="https://computerscienceresources.com/images/LogoTitle.svg" />
            <link head-key="resource-edits:canonical" rel="canonical" href="https://computerscienceresources.com/resource-edits" />
            <meta head-key="resource-edits:og:url" property="og:url" content="https://computerscienceresources.com/resource-edits" />
            <meta head-key="resource-edits:og:site_name" property="og:site_name" content="Computer Science Resources" />
        </Head>

        <div class="max-w-[90vw] mx-auto sm:px-6 lg:px-10 py-4">
            <!-- Page Header and Sorting -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-4">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-xl font-semibold text-primary">
                        Proposed Resource Edits
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Review and vote on community-proposed changes to resources
                    </p>
                </div>
                <div class="px-6 py-3">
                    <UpvoteSorting
                        route-name="resource_edits.index"
                        :route-params="{}"
                        :initial-value="props.sortingType"
                    />
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Resource Edits Table -->
                <section class="w-full lg:w-3/4">
                    <ResourceEditsTable :resource-edits="props.resource_edits" />
                </section>

                <!-- FAQ Sidebar -->
                <aside class="w-full lg:w-1/4">
                    <div class="lg:sticky lg:top-4">
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <ResourceEditsFAQ />
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>
