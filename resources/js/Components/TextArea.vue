<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
    rows: {
        type: Number,
        default: 4
    },
    placeholder: {
        type: String,
        default: ''
    }
});

defineEmits(['update:modelValue']);

const textarea = ref(null);

onMounted(() => {
    if (textarea.value.hasAttribute('autofocus')) {
        textarea.value.focus();
    }
});

defineExpose({ focus: () => textarea.value.focus() });
</script>

<template>
    <textarea
        ref="textarea"
        class="w-full border border-primary/30 rounded-md shadow-sm px-4 py-2
               placeholder:text-gray
               text-gray
               focus:border-primary focus:ring focus:ring-primary/20
               hover:border-primary/50
               transition-colors duration-200
               bg-white dark:bg-gray-900
               "
        :value="modelValue"
        :rows="rows"
        :placeholder="placeholder"
        @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>
</template>
