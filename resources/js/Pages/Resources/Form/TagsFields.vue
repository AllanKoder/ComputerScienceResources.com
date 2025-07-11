<script setup>
import { ref, defineProps, defineEmits, watch } from "vue";
import TagSelector from "@/Components/Form/TagSelector.vue";
import Button from "primevue/button";
import { yupResolver } from "@primevue/forms/resolvers/yup";
import { Form } from "@primevue/forms";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";
import { optionalFields } from "@/Helpers/validation";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["change", "next", "back"]);

// Reactive reference for form data
const localFormData = ref({
    programming_language_tags: props.form.programming_language_tags || [],
    general_tags: props.form.general_tags || [],
});
const errors = ref([]);
const schema = optionalFields;
const resolver = ref(yupResolver(schema));
const validateAndNext = async () => {
    schema
        .validate(localFormData.value)
        .then(() => emit("next"))
        .catch((error) => {
            errors.value = error.errors;
        });
};

// Update change
watch(
    localFormData,
    (newValue) => {
        emit("change", newValue);
    },
    { deep: true }
);
</script>

<template>
    <Form
        :resolver="resolver"
        :initialValues="localFormData"
        class="flex flex-col gap-4 w-full"
    >
        <div class="flex flex-col gap-1 justify-center items-center">
            <!-- Tag Selector for Programming Languages -->
            <h2 class="text-2xl font-bold mb-4 text-center">
                What Programming Languages are used (if any)?
            </h2>
            <TagSelector v-model="localFormData.programming_language_tags" />

            <!-- Tag Selector for Other tags -->
            <h2 class="text-2xl font-bold mb-4 text-center">
                What else is it related to?
            </h2>
            <TagSelector v-model="localFormData.general_tags" />

            <PrimeVueFormError :errors="errors" />
        </div>

        <!-- Prev/Next Button -->
        <div class="flex pt-6 justify-between">
            <Button
                @click="() => emit('back')"
                label="Back"
                severity="secondary"
                icon="pi pi-arrow-left"
            />
            <Button
                @click="validateAndNext"
                label="Submit"
                icon="pi pi-arrow-right"
                iconPos="right"
            />
        </div>
    </Form>
</template>
