<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? "inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium rounded-md text-white bg-primary hover:bg-primaryDark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/50 transition duration-200"
        : "inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-secondary dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/30 transition duration-200";
});
</script>

<template>
    <Link :href="href" :class="classes" :only="['resources', 'filters']">
        <slot />
    </Link>
</template>
