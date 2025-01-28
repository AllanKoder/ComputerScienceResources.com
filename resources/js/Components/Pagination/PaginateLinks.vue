<template>
    <div class="flex items-center justify-center space-x-2">
        <!-- Left Button -->
        <PaginateLink 
                v-if="previousLink" 
                :href="previousLink" 
                class="flex items-center justify-center"
            >
                <Icon icon="mdi:chevron-left" />
            </PaginateLink>

            <!-- Page Numbers -->
            <template v-if="links.length > 0">
                <span 
                    v-for="(link, index) in links.slice(1, -1)" 
                    :key="index" 
                    class="cursor-pointer"
                >
                    <PaginateLink 
                        v-if="link.url" 
                        :href="link.url" 
                        :active="link.active"
                    >
                        {{ link.label }}
                    </PaginateLink>
                    <span v-else class="px-3 py-1 border rounded text-gray-500">{{ link.label }}</span>
                </span>
            </template>

            <!-- Right Button -->
            <PaginateLink 
                v-if="nextLink" 
                :href="nextLink" 
                class="flex items-center justify-center"
            >
                <Icon icon="mdi:chevron-right" />
            </PaginateLink>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';
import { Icon } from '@iconify/vue';
import PaginateLink from '@/Components/Pagination/PaginateLink.vue'

const props = defineProps({
    links: {
        type: Array,
        required: true
    }
});

// Get the previous and next links
const previousLink = props.links[0]?.url; // Optional chaining to avoid errors
const nextLink = props.links[props.links.length - 1]?.url; // Optional chaining to avoid errors

</script>
