<script setup>
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import MultiSelect from "primevue/multiselect";
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

const props = defineProps({
    form: {
        type: Object,
        required: true
    },
});

const emit = defineEmits(["change"]);

watch(
    () => props.form,
    (newValue) => {
        emit("change", newValue);
    },
    { deep: true }
);

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

const resolver = yupResolver(schema);

</script>

<template>
    <Form
        :resolver="resolver"
        @submit="() => console.log('test')"
        class="flex flex-col gap-4 w-full"
    >
        <div class="space-y-4">
            <FormField v-slot="{ field, errors }" name="name">
                <label class="block text-sm font-medium text-gray-700"
                    >Name</label
                >
                <InputText
                    v-bind="field"
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
                    :class="{ 'p-invalid': errors.length > 0 }"
                    placeholder="Enter image URL"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError :errors="errors" />
            </FormField>

            <!-- Displaying the Image Preview -->
            <div v-if="form.imageUrl" class="mt-4">
                <h3 class="text-lg font-semibold">Image Preview:</h3>
                <img
                    :src="form.imageUrl"
                    alt="Resource Image"
                    class="mt-2 border rounded shadow-md"
                    style="max-width: 100%; height: auto"
                />
            </div>

            <FormField v-slot="{ field, errors }" name="formats">
                <label class="block text-sm font-medium text-gray-700"
                    >Resource Format</label
                >
                <MultiSelect
                    v-bind="field"
                    :options="resourceFormats"
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
                    option-label="label"
                    option-value="value"
                    placeholder="Select Pricing"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError :errors="errors" />
            </FormField>
        </div>
    </Form>
</template>
