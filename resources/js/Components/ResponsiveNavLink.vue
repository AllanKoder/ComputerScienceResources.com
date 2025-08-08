<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
});

const classes = computed(() => {
    return props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-primary dark:border-primaryDark text-start text-base font-medium text-primaryDark dark:text-primary bg-secondary dark:bg-secondaryDark focus:outline-none focus:text-primary dark:focus:text-primaryDark focus:bg-accent/30 dark:focus:bg-primaryDark/30 focus:border-primary dark:focus:border-primaryDark transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-599 dark:text-gray-400 hover:text-primary dark:hover:text-primaryDark hover:bg-accent/30 dark:hover:bg-primaryDark/30 hover:border-primary dark:hover:border-primaryDark focus:outline-none focus:text-primary dark:focus:text-primaryDark focus:bg-accent/30 dark:focus:bg-primaryDark/30 focus:border-primary dark:focus:border-primaryDark transition duration-150 ease-in-out';
});
</script>

<template>
    <div>
        <button v-if="as == 'button'" :class="classes" class="w-full text-start">
            <slot />
        </button>

        <a v-else-if="as == 'a'" :class="classes" class="w-full text-start" :href="href">
            <slot />
        </a>

        <Link v-else :href="href" :class="classes">
            <slot />
        </Link>
    </div>
</template>
