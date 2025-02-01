<script setup>
import Message from "primevue/message";
import { defineProps } from "vue";

const props = defineProps({
    errors: {
        type: Array,
        required: true,
    },
});

// Helper function to remove "ValidationError:" prefix
const formatError = (error) => error.replace(/^ValidationError:/, "");
</script>

<template>
    <Message
        v-if="errors.length"
        severity="error"
        size="small"
        variant="simple"
    >
        <template v-for="error in errors" :key="error">
            {{ formatError(error.toString()) }}
        </template>
    </Message>
</template>
