<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    as: String,
});

const dropdownLinkClasses = "block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out";
</script>

<template>
    <div>
        <button v-if="as === 'button'" :class="dropdownLinkClasses" class="w-full text-start">
            <slot />
        </button>

        <a v-else-if="as === 'a'" :class="dropdownLinkClasses" class="w-full text-start" :href="href">
            <slot />
        </a>

        <Link v-else :href="href" :class="dropdownLinkClasses">
            <slot />
        </Link>
    </div>
</template>
