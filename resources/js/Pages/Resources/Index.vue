<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import FilterBar from "@/Components/Resources/FilterBar.vue";
import ResourcesTable from "@/Components/Resources/ResourcesTable.vue";
import NewsSection from "@/Components/News/NewsSection.vue";

const props = defineProps({
    resources: Object,
    newsItems: Array,
});

const newsItems = [
    {
        id: 1,
        title: "New AI Breakthrough in Natural Language Processing",
        thumbnail:
            "https://images.pexels.com/photos/674010/pexels-photo-674010.jpeg?cs=srgb&dl=pexels-anjana-c-169994-674010.jpg&fm=jpg",
        excerpt:
            "Researchers have achieved a significant milestone in AI language understanding, potentially revolutionizing human-computer interactions.",
        date: "2025-01-23",
    },
    {
        id: 2,
        title: "Quantum Computing Reaches New Heights",
        thumbnail:
            "https://images.pexels.com/photos/674010/pexels-photo-674010.jpeg?cs=srgb&dl=pexels-anjana-c-169994-674010.jpg&fm=jpg",
        excerpt:
            "Scientists have successfully demonstrated a 1000-qubit quantum computer, bringing us closer to practical quantum supremacy.",
        date: "2025-01-24",
    },
    {
        id: 3,
        title: "Cybersecurity Alert: Major Vulnerability Discovered",
        thumbnail:
            "https://images.pexels.com/photos/674010/pexels-photo-674010.jpeg?cs=srgb&dl=pexels-anjana-c-169994-674010.jpg&fm=jpg",
        excerpt:
            "A critical flaw in widely-used software has been identified. Experts urge immediate patching to prevent potential exploits.",
        date: "2025-01-25",
    },
    {
        id: 4,
        title: "Tech Giants Collaborate on Open-Source AI Initiative",
        thumbnail:
            "https://images.pexels.com/photos/674010/pexels-photo-674010.jpeg?cs=srgb&dl=pexels-anjana-c-169994-674010.jpg&fm=jpg",
        excerpt:
            "Leading tech companies announce a joint effort to develop and release open-source AI tools, aiming to democratize AI technology.",
        date: "2025-01-25",
    },
];
</script>

<template>
    <AppLayout title="Computer Science Resources">
        <div class="max-w-[90vw] mx-auto sm:px-6 lg:px-10 py-4">
            <!-- Filter bar -->
            <FilterBar></FilterBar>

            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Resources Section -->
                <section class="w-full lg:w-3/4">
                    <ResourcesTable :resources="props.resources" />
                </section>

                <NewsSection :news-items="newsItems" />
            </div>
        </div>
    </AppLayout>
</template>
