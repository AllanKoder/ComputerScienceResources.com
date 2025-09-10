<script setup>
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  href: { type: String, required: true }
});
</script>

<template>
  <Link :href="href" class="group flex items-center gap-1">
    <slot />
    <Icon icon="mdi:chevron-right" class="text-primary group-hover:translate-x-1 transition-transform duration-200" width="22" height="22" />
  </Link>
</template>
