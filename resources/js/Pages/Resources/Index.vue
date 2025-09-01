<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import { Head } from '@inertiajs/vue3';
import FilterBar from "@/Components/Resources/FilterBar.vue";
import ResourcesTable from "@/Components/Resources/ResourcesTable.vue";
import NewsSection from "@/Components/News/NewsSection.vue";
const props = defineProps({
    resources: Object,
    news_posts: Array,
});

</script>

<template>
    <AppLayout title="Computer Science Resources">
        <!-- Page metadata -->
        <Head>
            <meta head-key="resources:description" name="description" content="Discover curated computer science resources: courses, blogs, newsletters, youtube channels; Community-vetted and open to contribution!" />
            <meta head-key="resources:og:title" property="og:title" content="Computer Science Resources" />
            <meta head-key="resources:og:description" property="og:description" content="Discover curated computer science resources: courses, blogs, newsletters, youtube channels; Community-vetted and open to contribution!" />
            <meta head-key="resources:og:type" property="og:type" content="website" />
            <meta head-key="resources:og:image" property="og:image" content="https://computerscienceresources.com/images/LogoTitle.svg" />
            <link head-key="resources:canonical" rel="canonical" href="https://computerscienceresources.com/" />
            <meta head-key="resources:og:url" property="og:url" content="https://computerscienceresources.com/" />
            <meta head-key="resources:og:site_name" property="og:site_name" content="Computer Science Resources" />
        </Head>

        <div class="max-w-[90vw] mx-auto sm:px-6 lg:px-10 py-4">
            <!-- Filter bar -->
            <FilterBar/>

            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Resources Section -->
                <section class="w-full lg:w-3/4">
                    <ResourcesTable :resources="props.resources" />
                </section>

                <NewsSection :news-posts="news_posts" />
            </div>
        </div>
    </AppLayout>
</template>
