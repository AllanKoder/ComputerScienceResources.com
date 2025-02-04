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
import { defineProps, defineEmits, watch, ref } from "vue";
import {
    platforms,
    pricings,
    difficulties,
} from "@/Helpers/constants";
import { reactive } from "vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["change", "next"]);

// The form data that is being filled out
const formData = reactive({
    ...props.form,
});

// The validation schema
const schema = object({
    name: string().required("Name is required").max(100, "Max 100 chars"),
    pageUrl: string().url("Must be a valid URL").required("URL is required"),
    imageUrl: string().url("Must be a valid image URL"),
    platforms: array()
        .of(string())
        .min(1, "At least one platform is required"),
    description: string().required("Description is required").max(4000),
    difficulty: string().required("Difficulty level is required"),
    pricing: string().required("Pricing information is required"),
});

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

const validateAndNext = () => {
    // Validate the form using the schema
    schema
        .validate(formData)
        .then((validData) => {
            emit("next", validData);
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
        :initialValues="formData"
        class="flex flex-col gap-4 w-full"
    >
        <div class="space-y-4">
            <!-- Name Field -->
            <FormField v-slot="$field" name="name" class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700"
                    >Name</label
                >
                <InputText
                    v-model="formData.name"
                    placeholder="Enter the Name"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- URL Field -->
            <FormField v-slot="$field" name="url" class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700"
                    >Resource Website URL</label
                >
                <InputText
                    v-model="formData.pageUrl"
                    placeholder="Enter resource URL"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- Image URL Field -->
            <FormField
                v-slot="$field"
                name="imageUrl"
                class="flex flex-col gap-1"
            >
                <label class="block text-sm font-medium text-gray-700"
                    >Image URL</label
                >
                <InputText
                    v-model="formData.imageUrl"
                    placeholder="Enter image URL"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />

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

            <FormField
                v-slot="$field"
                name="platforms"
                class="flex flex-col gap-1"
            >
                <!-- Resource Platforms Field -->
                <label class="block text-sm font-medium text-gray-700"
                    >Resource Platforms</label
                >
                <MultiSelect
                    v-model="formData.platforms"
                    :options="platforms"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Resource Platform"
                    class="w-full"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- Description Field -->
            <FormField
                v-slot="$field"
                name="description"
                class="flex flex-col gap-1"
            >
                <label class="block text-sm font-medium text-gray-700"
                    >Description</label
                >
                <Textarea
                    v-model="formData.description"
                    placeholder="Describe the resource..."
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    rows="3"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- Difficulty Field -->
            <FormField
                v-slot="$field"
                name="difficulty"
                class="flex flex-col gap-1"
            >
                <label class="block text-sm font-medium text-gray-700"
                    >Difficulty</label
                >
                <Select
                    :options="difficulties"
                    v-model="formData.difficulty"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Difficulty Level"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- Pricing Field -->
            <FormField
                v-slot="$field"
                name="pricing"
                class="flex flex-col gap-1"
            >
                <label class="block text-sm font-medium text-gray-700"
                    >Pricing</label
                >
                <Select
                    :options="pricings"
                    v-model="formData.pricing"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Pricing"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>
        </div>
    </Form>
    <!-- continue Button -->
    <div class="flex pt-6 justify-end">
        <Button
            label="Next"
            icon="pi pi-arrow-right"
            @click="validateAndNext"
        />
    </div>
</template>
