<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { yupResolver } from "@primevue/forms/resolvers/yup";

// PrimeVue Components
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import MultiSelect from "primevue/multiselect";
import Select from "primevue/select";
import { Button } from "primevue";
import { Form, FormField } from "@primevue/forms";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";

// Custom Components
import TagSelector from "@/Components/Form/TagSelector.vue";

// Helpers and Constants
import { platformsObject, pricingsObject, difficultiesObject } from "@/Helpers/labels";
import {
    resourceEditsMandatoryFields,
    resourceMandatoryFields,
    resourceMandatoryTags,
} from "@/Helpers/validation";

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
});

// Validation Schema for name
const schema = resourceMandatoryFields.concat(resourceMandatoryTags).concat(resourceEditsMandatoryFields);
// PrimeVue Resolver
const resolver = ref(yupResolver(schema));

// Create a form with initial values taken from the resource prop
const formData = useForm({
    // Related to the edit
    edit_title: "",
    edit_description: "",
    // The resource
    name: props.resource.name,
    description: props.resource.description,
    page_url: props.resource.page_url,
    image_url: props.resource.image_url,
    difficulty: props.resource.difficulty,
    pricing: props.resource.pricing,
    platforms: props.resource.platforms,
    topic_tags: props.resource.topic_tags || [],
    programming_language_tags: props.resource.programming_language_tags || [],
    general_tags: props.resource.general_tags || [],
});

// Function to handle form submission
const submit = () => {
    // Prepare the form data
    schema.validate(formData.data).then((validData) => {
        console.log("Posted: " + validData);
        formData.post(
            route("resource_edits.store", {
                computerScienceResource: props.resource.id,
            }),
            validData
        );
    });
};
</script>

<template>
    <AppLayout :title="formData.name">
        <Head :title="formData.name" />
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Form
                    :resolver="resolver"
                    :initialValues="formData"
                    @submit="submit"
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg"
                >
                    <!-- Edit title and description (reasoning for the edit) -->
                    <div class="p-3 m-3">
                        <!-- title Field -->
                        <FormField v-slot="$field" name="edit_title" class="mb-4">
                            <InputText
                                v-model="formData.edit_title"
                                placeholder="Title of your Edit"
                                class="w-full text-3xl font-bold border-b border-gray-300 focus:outline-none focus:border-blue-500"
                            />
                            <PrimeVueFormError
                                v-if="$field?.invalid"
                                :errors="$field.errors"
                            />
                        </FormField>

                        <!-- Description Field -->
                        <FormField
                            v-slot="$field"
                            name="edit_description"
                            class="mb-4"
                        >
                            <Textarea
                                v-model="formData.edit_description"
                                placeholder="Reason for the changes"
                                class="w-full border border-gray-300 rounded"
                                rows="4"
                            />
                            <PrimeVueFormError
                                v-if="$field?.invalid"
                                :errors="$field.errors"
                            />
                        </FormField>
                    </div>
                    <div class="p-6 sm:p-8">
                        <div class="relative">
                            <div
                                class="flex flex-col md:flex-row items-start mb-6"
                            >
                                <div class="flex-grow">
                                    <!-- Name Field -->
                                    <FormField
                                        v-slot="$field"
                                        name="name"
                                        class="mb-4"
                                    >
                                        <InputText
                                            v-model="formData.name"
                                            placeholder="Resource Name"
                                            class="w-full text-3xl font-bold border-b border-gray-300 focus:outline-none focus:border-blue-500"
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
                                        class="mb-4"
                                    >
                                        <Textarea
                                            v-model="formData.description"
                                            placeholder="Describe the resource..."
                                            class="w-full border border-gray-300 rounded"
                                            rows="4"
                                        />
                                        <PrimeVueFormError
                                            v-if="$field?.invalid"
                                            :errors="$field.errors"
                                        />
                                    </FormField>

                                    <!-- URL Field -->
                                    <FormField
                                        v-slot="$field"
                                        name="page_url"
                                        class="mb-4"
                                    >
                                        <InputText
                                            v-model="formData.page_url"
                                            placeholder="Resource URL"
                                            class="w-full border border-gray-300 rounded"
                                        />
                                        <PrimeVueFormError
                                            v-if="$field?.invalid"
                                            :errors="$field.errors"
                                        />
                                    </FormField>

                                    <!-- Image URL Field -->
                                    <FormField
                                        v-slot="$field"
                                        name="image_url"
                                        class="mb-4"
                                    >
                                        <InputText
                                            v-model="formData.image_url"
                                            placeholder="Image URL"
                                            class="w-full border border-gray-300 rounded"
                                        />
                                        <PrimeVueFormError
                                            v-if="$field?.invalid"
                                            :errors="$field.errors"
                                        />
                                    </FormField>

                                    <!-- Platforms Field -->
                                    <FormField
                                        v-slot="$field"
                                        name="platforms"
                                        class="mb-4"
                                    >
                                        <MultiSelect
                                            v-model="formData.platforms"
                                            :options="platformsObject"
                                            option-label="label"
                                            option-value="value"
                                            placeholder="Select Resource Platforms"
                                            class="w-full"
                                        />
                                        <PrimeVueFormError
                                            v-if="$field?.invalid"
                                            :errors="$field.errors"
                                        />
                                    </FormField>

                                    <!-- Difficulty and Pricing Fields -->
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4"
                                    >
                                        <FormField
                                            v-slot="$field"
                                            name="difficulty"
                                        >
                                            <Select
                                                :options="difficultiesObject"
                                                v-model="formData.difficulty"
                                                option-label="label"
                                                option-value="value"
                                                placeholder="Select Difficulty"
                                                class="w-full"
                                            />
                                            <PrimeVueFormError
                                                v-if="$field?.invalid"
                                                :errors="$field.errors"
                                            />
                                        </FormField>

                                        <FormField
                                            v-slot="$field"
                                            name="pricing"
                                        >
                                            <Select
                                                :options="pricingsObject"
                                                v-model="formData.pricing"
                                                option-label="label"
                                                option-value="value"
                                                placeholder="Select Pricing"
                                                class="w-full"
                                            />
                                            <PrimeVueFormError
                                                v-if="$field?.invalid"
                                                :errors="$field.errors"
                                            />
                                        </FormField>
                                    </div>
                                </div>
                            </div>

                            <!-- Tag Selectors -->
                            <div class="mb-4">
                                <FormField
                                    v-slot="$field"
                                    name="topic_tags"
                                    class="mb-4"
                                >
                                    <p class="font-bold mb-4 text-center">
                                        Topics?
                                    </p>

                                    <TagSelector
                                        :initial="formData.topic_tags"
                                        :query-url="''"
                                        @changed="
                                            (tags) =>
                                                (formData.topic_tags = tags)
                                        "
                                    />

                                    <PrimeVueFormError
                                        v-if="$field?.invalid"
                                        :errors="$field.errors"
                                    />
                                </FormField>
                            </div>

                            <div class="mb-4">
                                <p class="font-bold mb-4 text-center">
                                    What Programming Languages are used (if
                                    any)?
                                </p>
                                <TagSelector
                                    :initial="
                                        formData.programming_language_tags
                                    "
                                    :query-url="''"
                                    @changed="
                                        (tags) =>
                                            (formData.programming_language_tags =
                                                tags)
                                    "
                                />
                            </div>

                            <div class="mb-4">
                                <h2 class="font-bold mb-4 text-center">
                                    What else is it related to?
                                </h2>
                                <TagSelector
                                    :initial="formData.general_tags"
                                    :query-url="''"
                                    @changed="
                                        (tags) => (formData.general_tags = tags)
                                    "
                                />
                            </div>

                            <!-- Submission Details -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4"
                            >
                                <a
                                    :href="props.resource.page_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-600 hover:underline mb-2 sm:mb-0"
                                    >Visit Resource</a
                                >
                                <div class="text-sm text-gray-500">
                                    <p>
                                        Posted by:
                                        {{
                                            props.resource.user?.name ??
                                            "Unknown User"
                                        }}
                                    </p>
                                    <p>
                                        Created:
                                        {{
                                            new Date(
                                                props.resource.created_at
                                            ).toLocaleString()
                                        }}
                                    </p>
                                    <p>
                                        Last updated:
                                        {{
                                            new Date(
                                                props.resource.updated_at
                                            ).toLocaleString()
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <Button
                                type="submit"
                                label="Save Changes"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-150"
                            />
                        </div>
                    </div>
                </Form>
            </div>
        </main>
    </AppLayout>
</template>
