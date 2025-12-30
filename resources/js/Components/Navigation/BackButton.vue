<script setup>
import { defineProps } from "vue";
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    route: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div>
        <Link :href="props.route">
            <div class="flex flex-row gap-2">
                <Icon icon="mdi:arrow-back" class="text-xl my-auto"></Icon>
                <slot />
            </div>
        </Link>
        <div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>
    </div>
</template>
