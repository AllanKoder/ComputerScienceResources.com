<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    resource: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Link :href="route('resources.show', { slug: resource.slug }) || '#'" target="_blank" rel="noopener noreferrer">
        <div class="my-4 border hover:bg-gray-50 dark:hover:bg-gray-700 p-2 rounded-sm transition-colors cursor-pointer">
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                <span v-if="resource.upvotes_count || resource.vote_score" class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                    </svg>
                    {{ resource.upvotes_count || resource.vote_score }}
                </span>
            </div>

            <h3 class="font-bold mb-2 text-md dark:text-white line-clamp-2">
                {{ resource.name }}
            </h3>

            <img
                v-if="resource.image_url "
                :src="resource.image_url"
                :alt="resource.name"
                class="w-full h-32 object-cover rounded mb-2"
            />

            <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3 mb-2">
                {{ resource.excerpt || resource.description }}
            </p>

        </div>
    </Link>
</template>
