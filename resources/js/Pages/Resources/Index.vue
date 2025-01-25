<script setup>
import { Icon } from "@iconify/vue";
import { ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";

const props = defineProps({
    resources: Array,
});

const resources = ref(props.resources);

const upvote = (resource) => {
    // Implement upvote logic
};

const downvote = (resource) => {
    // Implement downvote logic
};
</script>

<template>
    <AppLayout title="Computer Science Resources">

        <main class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <section
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <table
                        class="w-full border-separate"
                        style="border-spacing: 0 2rem"
                    >
                        <tbody>
                            <tr
                                v-for="resource in resources"
                                :key="resource.id"
                                class="mb-8 border-b p-12"
                            >
                                <td class="align-top pr-6">
                                    <div class="flex flex-col items-center">
                                        <button
                                            @click="upvote(resource)"
                                            class="mb-2 text-gray-500 hover:text-blue-500"
                                        >
                                            <Icon
                                                icon="mdi:chevron-up"
                                                width="24"
                                                height="24"
                                            />
                                        </button>
                                        <span class="text-lg font-bold">{{
                                            resource.votes || 0
                                        }}</span>
                                        <button
                                            @click="downvote(resource)"
                                            class="mt-2 text-gray-500 hover:text-red-500"
                                        >
                                            <Icon
                                                icon="mdi:chevron-down"
                                                width="24"
                                                height="24"
                                            />
                                        </button>
                                    </div>
                                </td>

                                <td class="align-top pr-6 w-32">
                                    <img
                                        :src="resource.image_url"
                                        :alt="resource.name"
                                        class="w-full h-auto object-contain rounded"
                                    />
                                </td>

                                <td class="align-top pr-6">
                                    <div
                                        class="flex justify-between items-start mb-3"
                                    >
                                        <h2 class="text-xl font-semibold">
                                            {{ resource.name }}
                                        </h2>
                                        <time class="text-sm text-gray-500">{{
                                            resource.resource_created_on
                                        }}</time>
                                    </div>
                                    <p class="text-gray-600 mb-4">
                                        {{ resource.description }}
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="type in resource.resource_type.split(
                                                ','
                                            )"
                                            :key="type"
                                            class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded"
                                        >
                                            {{ type.trim() }}
                                        </span>
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded"
                                        >
                                            {{ resource.pricing }}
                                        </span>
                                    </div>
                                </td>

                                <td class="align-top">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="text-2xl font-bold text-yellow-500 mb-2"
                                        >
                                            4.5
                                        </div>
                                        <div class="flex">
                                            <Icon
                                                icon="mdi:star"
                                                class="text-yellow-500"
                                                width="20"
                                                height="20"
                                            />
                                            <Icon
                                                icon="mdi:star"
                                                class="text-yellow-500"
                                                width="20"
                                                height="20"
                                            />
                                            <Icon
                                                icon="mdi:star"
                                                class="text-yellow-500"
                                                width="20"
                                                height="20"
                                            />
                                            <Icon
                                                icon="mdi:star"
                                                class="text-yellow-500"
                                                width="20"
                                                height="20"
                                            />
                                            <Icon
                                                icon="mdi:star-half"
                                                class="text-yellow-500"
                                                width="20"
                                                height="20"
                                            />
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            (123 ratings)
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </div>
        </main>
    </AppLayout>
</template>
