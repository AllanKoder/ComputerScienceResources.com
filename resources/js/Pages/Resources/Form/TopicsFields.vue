<script setup>
import { ref, defineProps, defineEmits, watch } from "vue";
import TagSelector from "@/Components/Form/TagSelector.vue";
import Message from "primevue/message";
import Button from "primevue/button";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["change", "next", "back"]);

// Reactive reference for form data
const formData = ref({ ...props.form });

// Update change
watch(
    formData,
    (newValue) => {
        emit("change", newValue);
    },
    { deep: true }
);

const validate = () => {
    return formData.value.topic_tags && formData.value.topic_tags.length >= 3;
};

const validationError = ref("");
// Function to handle form submission
const validateAndNext = async () => {
    if (validate()) {
        emit("next");
    } else {
        console.error("Validation failed");
        validationError.value = "Must have at least 3 topics";
    }
};
</script>

<template>
    <h2 class="text-2xl font-bold mb-4 text-center">
        What topics does this resource cover?
    </h2>

    <!-- Show error message if validation fails -->
    <Message
        v-if="validationError"
        severity="error"
        size="small"
        variant="simple"
    >
        {{ validationError }}
    </Message>

    <!-- Tag Selector for topics -->
    <TagSelector
        :initial="formData.topic_tags ?? []"
        @changed="(tags) => (formData.topic_tags = tags)"
    ></TagSelector>

    <!-- Prev/Next Button -->
    <div class="flex pt-6 justify-between">
        <Button
            label="Back"
            severity="secondary"
            icon="pi pi-arrow-left"
            @click="() => emit('back')"
        />

        <Button
            label="Next"
            icon="pi pi-arrow-right"
            iconPos="right"
            @click="validateAndNext"
        />
    </div>
</template>
