<script setup>
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import MultiSelect from "primevue/multiselect";
import { Button } from "primevue";
import { Form, FormField } from "@primevue/forms";
import { yupResolver } from "@primevue/forms/resolvers/yup";
import { object, string, array } from "yup";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";

import Select from "primevue/select";
import { defineProps, defineEmits, watch } from "vue";
import {
    resourceFormats,
    pricingOptions,
    difficultyLevels,
} from "@/Helpers/constants";
import { reactive } from "vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["change", "submit"]);

// The form data that is being filled out
const formData = reactive({ ...props.form });

// The validation schema
const schema = object({
    name: string().required("Name is required"),
    url: string().url("Must be a valid URL").required("URL is required"),
    imageUrl: string().url("Must be a valid image URL"),
    formats: array()
        .of(string())
        .default([])
        .min(1, "At least one resource format is required"),
    description: string().required("Description is required"),
    difficulty: string().required("Difficulty level is required"),
    pricing: string().required("Pricing information is required"),
});

// PrimeVue Resolver
const resolver = yupResolver(schema);

// Update change
watch(
    formData,
    (newValue) => {
        emit("change", newValue);
    },
    { deep: true }
);

const validateAndSubmit = () => {
    // Validate the form using the schema
    schema
        .validate(formData)
        .then((validData) => {
            console.log("succesffu;");
            console.log(validData);
            emit("submit", validData);
        })
        .catch((error) => {
            // If validation fails, you can handle the errors here
            console.error("Validation failed:", error.errors);
        });
};
</script>

<template>
    <Form
        :resolver="resolver"
        class="flex flex-col gap-4 w-full"
    >
        <div class="space-y-4">
            <FormField v-slot="{ field, errors }" name="name">
                <label class="block text-sm font-medium text-gray-700"
                    >Name</label
                >
                <InputText
                    v-bind="field"
                    v-model="formData.name"
                    placeholder="Enter the Name"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />

                <PrimeVueFormError :errors="errors" />
            </FormField>

            <FormField v-slot="{ field, errors }" name="url">
                <label class="block text-sm font-medium text-gray-700"
                    >Resource Website URL</label
                >
                <InputText
                    v-bind="field"
                    v-model="formData.url"
                    placeholder="Enter resource URL"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />

                <PrimeVueFormError :errors="errors" />
            </FormField>

            <FormField v-slot="{ field, errors }" name="imageUrl">
                <label class="block text-sm font-medium text-gray-700"
                    >Image URL</label
                >
                <InputText
                    v-bind="field"
                    v-model="formData.imageUrl"
                    placeholder="Enter image URL"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />

                <PrimeVueFormError :errors="errors" />

                <!-- Displaying the Image Preview -->
                <div class="mt-4">
                    <h3 class="text-lg font-semibold">Image Preview:</h3>
                    <img
                        :src="formData.imageUrl"
                        alt="Resource Image"
                        class="mt-2 border rounded shadow-md"
                        style="max-width: 100%; height: auto"
                    />
                </div>
            </FormField>

            <FormField v-slot="{ field, errors }" name="formats">
                <label class="block text-sm font-medium text-gray-700"
                    >Resource Format</label
                >
                <MultiSelect
                    v-bind="field"
                    :options="resourceFormats"
                    v-model="formData.formats"
                    option-label="label"
                    option-value="value"
                    placeholder="Select formats"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />

                <PrimeVueFormError :errors="errors" />
            </FormField>

            <FormField v-slot="{ field, errors }" name="description">
                <label class="block text-sm font-medium text-gray-700"
                    >Description</label
                >
                <Textarea
                    v-bind="field"
                    v-model="formData.description"
                    placeholder="Describe the resource..."
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    rows="3"
                />
                <PrimeVueFormError :errors="errors" />
            </FormField>

            <FormField v-slot="{ field, errors }" name="difficulty">
                <label class="block text-sm font-medium text-gray-700"
                    >Difficulty</label
                >
                <Select
                    v-bind="field"
                    :options="difficultyLevels"
                    v-model="formData.difficulty"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Difficulty Level"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError :errors="errors" />
            </FormField>

            <FormField v-slot="{ field, errors }" name="pricing">
                <label class="block text-sm font-medium text-gray-700"
                    >Pricing</label
                >
                <Select
                    v-bind="field"
                    :options="pricingOptions"
                    v-model="formData.pricing"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Pricing"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError :errors="errors" />
            </FormField>

            <Button
                label="Next"
                icon="pi pi-arrow-right"
                @click="validateAndSubmit"
            />
        </div>
    </Form>
</template>
