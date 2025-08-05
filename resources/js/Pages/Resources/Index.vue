<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
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
