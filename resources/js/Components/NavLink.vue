<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center h-min px-1 py-1 border-b-2 border-primary dark:border-primaryDark text-sm font-medium leading-5 text-primaryDark dark:text-secondary focus:outline-none focus:border-primaryDark transition duration-150 ease-in-out'
        : 'inline-flex items-center h-min px-1 py-1 border-b-2 border-transparent text-sm font-medium leading-5 text-primary dark:text-secondary hover:text-primaryDark dark:hover:text-secondaryDark hover:border-secondaryDark focus:outline-none focus:text-primaryDark dark:focus:text-secondaryDark focus:border-secondaryDark transition duration-150 ease-in-out';
});
</script>

<template>
    <div class="flex items-center -my-px">
        <Link :href="href" :class="classes">
            <slot />
        </Link>
    </div>
</template>
