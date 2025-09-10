<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="w-full border border-primary/30 rounded-md shadow-sm px-4 py-2
               placeholder:text-gray-400
               text-gray-800 dark:text-gray-200
               focus:border-primary focus:ring focus:ring-primary/20
               hover:border-primary/50
               transition-colors duration-200
               bg-white dark:bg-gray-900"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
