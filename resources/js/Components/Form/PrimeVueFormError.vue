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
function formatError(error) {
    return error
        .replace(/^ValidationError:\s*/, '') // Remove prefix and optional space
        .replace(/_/g, ' '); // Replace all underscores with spaces
}

</script>

<template>
    <Message
        v-if="errors.length"
        severity="error"
        size="small"
        variant="simple"
    >
        <template v-for="error in errors" :key="error">
            <p>
                {{ formatError(error.toString()) }}
            </p>
        </template>
    </Message>
</template>
