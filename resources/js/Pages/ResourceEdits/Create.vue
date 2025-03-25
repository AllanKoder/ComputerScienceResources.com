<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { yupResolver } from "@primevue/forms/resolvers/yup";
import { object, string, array } from "yup";

// PrimeVue Components
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import MultiSelect from "primevue/multiselect";
import Select from "primevue/select";
import { Button } from "primevue";
import { Form, FormField } from "@primevue/forms";
import Tag from "primevue/tag";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";

// Custom Components
import TagSelector from "@/Components/Form/TagSelector.vue";

// Helpers and Constants
import { platforms, pricings, difficulties } from "@/Helpers/constants";

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
});

// Validation Schema
const schema = object({
    name: string().required("Name is required").max(100, "Max 100 chars"),
    description: string().required("Description is required").max(4000),
    page_url: string().url("Must be a valid URL").required("URL is required"),
    image_url: string().url("Must be a valid image URL"),
    platforms: array().of(string()).min(1, "At least one platform is required"),
    difficulty: string().required("Difficulty level is required"),
    pricing: string().required("Pricing information is required"),
});

// Create a form with initial values taken from the resource prop
const form = useForm({
    name: props.resource.name,
    description: props.resource.description,
    page_url: props.resource.page_url,
    image_url: props.resource.image_url,
    difficulty: props.resource.difficulty,
    pricing: props.resource.pricing,
    platforms: props.resource.platforms
        ? props.resource.platforms.split(",").map((p) => p.trim())
        : [],
    programming_language_tags: props.resource.programming_language_tags || [],
    general_tags: props.resource.general_tags || [],
});

// Function to handle form submission
const submit = () => {
    // Prepare the form data
    const submitData = {
        ...form,
        platforms: form.platforms.join(","),
    };

    form.patch(route("resources.update", props.resource.id));
};

// Platform color mapping
const platformColors = {
    book: "blue",
    podcast: "green",
    youtube_channel: "red",
    blog: "orange",
    website: "purple",
    organization: "cyan",
    bootcamp: "pink",
    newsletter: "indigo",
    workshop: "teal",
    course: "yellow",
    forum: "gray",
    mobile_app: "lime",
    desktop_app: "amber",
    magazine: "rose",
};
</script>

<template>
    <AppLayout :title="form.name">
        <Head :title="form.name" />
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Form
                    :resolver="yupResolver(schema)"
                    :initialValues="form"
                    @submit.prevent="submit"
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg"
                >
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
                                            v-model="form.name"
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
                                            v-model="form.description"
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
                                            v-model="form.page_url"
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
                                            v-model="form.image_url"
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
                                            v-model="form.platforms"
                                            :options="platforms"
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
                                                :options="difficulties"
                                                v-model="form.difficulty"
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
                                                :options="pricings"
                                                v-model="form.pricing"
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
                                <p class="font-bold mb-4 text-center">
                                    Topics?
                                </p>
                                <TagSelector
                                    :initial="form.programming_language_tags"
                                    :queryUrl="''"
                                    @changed="
                                        (tags) =>
                                            (form.programming_language_tags =
                                                tags)
                                    "
                                />
                            </div>

                            <div class="mb-4">
                                <p class="font-bold mb-4 text-center">
                                    What Programming Languages are used (if
                                    any)?
                                </p>
                                <TagSelector
                                    :initial="form.programming_language_tags"
                                    :queryUrl="''"
                                    @changed="
                                        (tags) =>
                                            (form.programming_language_tags =
                                                tags)
                                    "
                                />
                            </div>

                            <div class="mb-4">
                                <h2 class="font-bold mb-4 text-center">
                                    What else is it related to?
                                </h2>
                                <TagSelector
                                    :initial="form.general_tags"
                                    :queryUrl="''"
                                    @changed="
                                        (tags) => (form.general_tags = tags)
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
