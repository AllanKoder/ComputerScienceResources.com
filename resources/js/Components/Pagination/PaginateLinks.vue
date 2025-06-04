<script setup>
import { defineProps } from "vue";
import { Icon } from "@iconify/vue";
import PaginateLink from "@/Components/Pagination/PaginateLink.vue";

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
});

// Get the previous and next links
const previousLink = props.links[0]?.url;
const nextLink = props.links[props.links.length - 1]?.url;
</script>

<template>
    <nav class="flex items-center justify-center">
        <div class="flex items-center space-x-1.5">
            <!-- Left Button -->
            <PaginateLink
                v-if="previousLink"
                :href="previousLink"
                class="!px-2"
            >
                <Icon icon="mdi:chevron-left" class="w-5 h-5" />
            </PaginateLink>

            <!-- Page Numbers -->
            <template v-if="links.length > 0">
                <span v-for="(link, index) in links.slice(1, -1)" :key="index">
                    <PaginateLink
                        v-if="link.url"
                        :href="link.url"
                        :active="link.active"
                    >
                        {{ link.label }}
                    </PaginateLink>
                    <span
                        v-else
                        class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-gray-500 dark:text-gray-400"
                    >
                        {{ "..." }}
                    </span>
                </span>
            </template>

            <!-- Right Button -->
            <PaginateLink v-if="nextLink" :href="nextLink" class="!px-2">
                <Icon icon="mdi:chevron-right" class="w-5 h-5" />
            </PaginateLink>
        </div>
    </nav>
</template>
