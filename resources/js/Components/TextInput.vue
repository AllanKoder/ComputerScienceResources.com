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
        class="border-primary/30 dark:border-primaryDark dark:bg-secondary dark:text-primary focus:border-primary dark:focus:border-primaryDark focus:ring-primary dark:focus:ring-primaryDark rounded-md shadow-sm bg-white text-gray-900"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
