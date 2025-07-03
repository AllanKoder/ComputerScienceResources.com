<script setup>
defineProps({
  field: {
    type: Object,
    required: true,
  },
});
</script>

<template>
    <div class="diff-container font-mono text-sm whitespace-pre-wrap">
        <span
            v-for="(part, index) in field.diffArray"
            :key="index"
            :class="{
                'bg-green-100/80 text-green-900': part.added,
                'bg-red-100/80 text-red-900 line-through': part.removed,
                'text-gray-600': !part.added && !part.removed,
            }"
        >
            {{ part.value }}
        </span>
    </div>
</template>
