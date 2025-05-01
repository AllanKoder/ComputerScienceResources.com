<script setup>
import { ref, defineProps, defineEmits, watch } from "vue";
import TagSelector from "@/Components/Form/TagSelector.vue";
import Button from "primevue/button";
import { yupResolver } from "@primevue/forms/resolvers/yup";
import { resourceMandatoryTags } from "@/Helpers/validation";
import { Form, FormField } from "@primevue/forms";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["change", "next", "back"]);

// Reactive reference for form data
const formData = ref({ ...props.form });
const errors = ref([]);

const schema = resourceMandatoryTags;
// PrimeVue Resolver
const resolver = ref(yupResolver(schema));

// Update change
watch(
    formData,
    (newValue) => {
        emit("change", newValue);
    },
    { deep: true }
);

// Function to handle form submission
const validateAndNext = async () => {
    schema
        .validate(formData.value)
        .then((_) => {
            console.log("validated");
            emit("next");
        })
        .catch((error) => {
            console.error("Validation failed:", error.errors);
            errors.value = error.errors;
        });
    console.log(formData);
};
</script>

<template>
    <h2 class="text-2xl font-bold mb-4 text-center">
        What topics does this resource cover?
    </h2>
    <Form
        :resolver="resolver"
        :initialValues="formData"
        class="flex flex-col gap-4 w-full"
    >
        <div class="flex flex-col gap-1">
            <!-- Tag Selector for topics -->
            <TagSelector
                :initial="formData.topic_tags ?? []"
                v-model="formData.topic_tags"
            ></TagSelector>
            <PrimeVueFormError :errors="errors" />
        </div>

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
    </Form>
</template>
