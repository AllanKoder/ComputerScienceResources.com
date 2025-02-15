<script setup>
import ResourceItem from "@/Components/Resources/ResourceItem.vue";
import NewsItem from "@/Components/NewsItem.vue";

import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import PaginateLinks from "@/Components/Pagination/PaginateLinks.vue";

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
        <main class="py-12">
            <div class="max-w-[110rem] mx-auto sm:px-6 lg:px-10">
                <div class="flex gap-4">
                    <!-- Resources Section -->
                    <section
                        class="w-3/4 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6"
                    >
                        <table
                            class="w-full border-separate"
                            style="border-spacing: 0 2rem"
                        >
                            <ResourceItem
                                v-for="resource in props.resources.data"
                                :key="resource.id"
                                :resource="resource"
                            />
                        </table>

                        <!-- Pagination Links -->
                        <PaginateLinks :links="props.resources.links"></PaginateLinks>
                    </section>

                    <!-- News Column -->
                    <aside
                        class="w-1/4 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6"
                    >
                        <h2 class="text-xl font-semibold mb-4">Latest News</h2>
                        <div class="space-y-4">
                            <NewsItem
                                v-for="(news, index) in newsItems"
                                :key="index"
                                :news="news"
                            />
                        </div>
                    </aside>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
