<script setup>
import { defineProps } from 'vue';

defineProps({
    src: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        required: true,
    }
})
</script>

<template>
    <img
        v-if="src"
        :src="src"
        :alt="alt"
        class="w-44 h-44 object-cover rounded-lg self-center p-1"
    />
    <img
        v-else
        src="/images/missing_resource.png"
        :alt="alt"
        class="w-44 h-44 object-cover rounded-lg self-center p-1"
    />
</template>
