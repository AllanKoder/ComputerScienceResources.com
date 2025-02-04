<script setup>
import { ref, defineProps, defineEmits, watch } from "vue";
import TagSelector from "@/Components/Form/TagSelector.vue";
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
</script>

<template>
    <!-- Tag Selector for Programming Languages -->
    <h2 class="text-2xl font-bold mb-4 text-center">
        What Programming Languages are used (if any)?
    </h2>
    <TagSelector
        :initial="formData.programmingLanguages ?? []"
        @changed="(tags) => (formData.programmingLanguages = tags)"
    ></TagSelector>

    <!-- Tag Selector for Other tags -->
    <h2 class="text-2xl font-bold mb-4 text-center">
        What else is it related to?
    </h2>
    <TagSelector
        :initial="formData.topics ?? []"
        @changed="(tags) => (formData.tags = tags)"
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
            label="Submit"
            icon="pi pi-arrow-right"
            iconPos="right"
            @click="() => emit('next')"
        />
    </div>
</template>
