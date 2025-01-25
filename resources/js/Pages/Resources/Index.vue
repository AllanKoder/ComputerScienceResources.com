<!-- resources/js/Pages/Resources/Index.vue -->
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps } from 'vue';

const props = defineProps({
    resources: Array,
});
</script>

<template>
    <AppLayout title="Computer Science Resources">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Computer Science Resources
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Image URL</th>
                                <th>Page URL</th>
                                <th>Created On</th>
                                <th>Resource Type</th>
                                <th>Difficulty</th>
                                <th>Pricing</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="resource in resources" :key="resource.id">
                                <td>{{ resource.name }}</td>
                                <td>{{ resource.description }}</td>
                                <td><img :src="resource.image_url" alt="" class="w-20"/></td>
                                <td><a :href="resource.page_url" target="_blank">Link</a></td>
                                <td>{{ resource.resource_created_on }}</td>
                                <td>{{ resource.resource_type }}</td>
                                <td>{{ resource.difficulty }}</td>
                                <td>{{ resource.pricing }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 12px;
    text-align: left;
}
img {
    max-width: 100%;
}
</style>
