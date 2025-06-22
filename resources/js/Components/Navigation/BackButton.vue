<script setup>
import { defineProps } from "vue";
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    route: {
        type: String,
        required: true,
    }
})

</script>

<template>
    <Link :href="props.route">
        <Icon icon="mdi:arrow-back" class="text-xl my-auto"></Icon>
    </Link>
</template>
