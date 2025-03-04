<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

import { Form, FormField } from "@primevue/forms";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";
import InputText from "primevue/inputtext";
import InputTextarea from "primevue/textarea";
import Button from "primevue/button";
import ListInput from "@/Components/ListInput.vue";

import { object, string, number } from "yup";
import { yupResolver } from "@primevue/forms/resolvers/yup";

const props = defineProps({
    resourceId: {
        type: Number,
        required: true,
    },
});

const form = useForm({
    title: "1",
    description: "1",
    community: "1",
    teaching_clarity: "1",
    engagement: "1",
    practicality: "1",
    user_friendliness: "1",
    updates: "1",
    pros: "[]",
    cons: "[]",
});

// Define the Yup schema. For the pros and cons fields we expect a valid JSON string representing an array.
const schema = object({
    title: string().required("Title is required."),
    description: string().required("Description is required."),
    community: string().required("Community is required."),
    teaching_clarity: number()
        .required("Teaching clarity is required.")
        .min(1, "Minimum is 1.")
        .max(5, "Maximum is 5."),
    engagement: number()
        .required("Engagement is required.")
        .min(1, "Minimum is 1.")
        .max(5, "Maximum is 5."),
    practicality: number()
        .required("Practicality is required.")
        .min(1, "Minimum is 1.")
        .max(5, "Maximum is 5."),
    user_friendliness: number()
        .required("User friendliness is required.")
        .min(1, "Minimum is 1.")
        .max(5, "Maximum is 5."),
    updates: number()
        .required("Updates rating is required.")
        .min(1, "Minimum is 1.")
        .max(5, "Maximum is 5."),
    pros: string()
        .required("Pros are required.")
        .test(
            "json-format",
            "Pros must have 200 characters or less.",
            (value) => {
                if (value.length > 200) return false;
                return true;
            }
        ),
    cons: string()
        .required("Cons are required.")
        .test(
            "json-format",
            "Cons must have 200 characters or less.",
            (value) => {
                if (value.length > 200) return false;
                return true;
            }
        ),
});

// Create the Yup resolver instance.
const resolver = ref(yupResolver(schema));

// On submission, validate using the schema and then submit via Inertia.
const submitReview = () => {
    console.log(form);
    schema
        .validate(form.data, { abortEarly: false })
        .then(() => {
            console.log("posted");
            form.post(
                route("reviews.store", {
                    computerScienceResource: props.resourceId,
                }),
                { preserveScroll: true }
            );
        })
        .catch((err) => {
            console.error("Validation errors:", err);
        });
};
</script>

<template>
    <Form
        :resolver="resolver"
        :initialValues="form"
        @submit="submitReview"
        class="flex flex-col gap-4 w-full"
    >
        <!-- Title Field -->
        <FormField v-slot="$field" name="title" class="flex flex-col gap-1">
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <InputText
                v-model="form.title"
                placeholder="Enter review title"
                class="mt-1 w-full border border-gray-300 rounded-md"
            />
            <PrimeVueFormError v-if="$field?.invalid" :errors="$field.errors" />
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
            <InputTextarea
                v-model="form.description"
                placeholder="Enter review description"
                class="mt-1 w-full border border-gray-300 rounded-md"
            />
            <PrimeVueFormError v-if="$field?.invalid" :errors="$field.errors" />
        </FormField>

        <!-- Community Field -->
        <FormField v-slot="$field" name="community" class="flex flex-col gap-1">
            <label class="block text-sm font-medium text-gray-700"
                >Community</label
            >
            <InputText
                v-model="form.community"
                placeholder="Enter community name"
                class="mt-1 w-full border border-gray-300 rounded-md"
            />
            <PrimeVueFormError v-if="$field?.invalid" :errors="$field.errors" />
        </FormField>

        <!-- Teaching Clarity Field -->
        <FormField
            v-slot="$field"
            name="teaching_clarity"
            class="flex flex-col gap-1"
        >
            <label class="block text-sm font-medium text-gray-700">
                Teaching Clarity (1-5)
            </label>
            <InputText
                v-model="form.teaching_clarity"
                placeholder="Rate clarity"
                class="mt-1 w-full border border-gray-300 rounded-md"
            />
            <PrimeVueFormError v-if="$field?.invalid" :errors="$field.errors" />
        </FormField>

        <!-- Engagement Field -->
        <FormField
            v-slot="$field"
            name="engagement"
            class="flex flex-col gap-1"
        >
            <label class="block text-sm font-medium text-gray-700">
                Engagement (1-5)
            </label>
            <InputText
                v-model="form.engagement"
                placeholder="Rate engagement"
                class="mt-1 w-full border border-gray-300 rounded-md"
            />
            <PrimeVueFormError v-if="$field?.invalid" :errors="$field.errors" />
        </FormField>

        <!-- Practicality Field -->
        <FormField
            v-slot="$field"
            name="practicality"
            class="flex flex-col gap-1"
        >
            <label class="block text-sm font-medium text-gray-700">
                Practicality (1-5)
            </label>
            <InputText
                v-model="form.practicality"
                placeholder="Rate practicality"
                class="mt-1 w-full border border-gray-300 rounded-md"
            />
            <PrimeVueFormError v-if="$field?.invalid" :errors="$field.errors" />
        </FormField>

        <!-- User Friendliness Field -->
        <FormField
            v-slot="$field"
            name="user_friendliness"
            class="flex flex-col gap-1"
        >
            <label class="block text-sm font-medium text-gray-700">
                User Friendliness (1-5)
            </label>
            <InputText
                v-model="form.user_friendliness"
                placeholder="Rate user friendliness"
                class="mt-1 w-full border border-gray-300 rounded-md"
            />
            <PrimeVueFormError v-if="$field?.invalid" :errors="$field.errors" />
        </FormField>

        <!-- Updates Field -->
        <FormField v-slot="$field" name="updates" class="flex flex-col gap-1">
            <label class="block text-sm font-medium text-gray-700">
                Updates (1-5)
            </label>
            <InputText
                v-model="form.updates"
                placeholder="Rate updates"
                class="mt-1 w-full border border-gray-300 rounded-md"
            />
            <PrimeVueFormError v-if="$field?.invalid" :errors="$field.errors" />
        </FormField>

        <!-- Pros Field using ListInput -->
        <FormField v-slot="$field" name="pros" class="flex flex-col gap-1">
            <label class="block text-sm font-medium text-gray-700">Pros</label>
            <ListInput
                :maxSize="10"
                :initialValues="form.pros ? JSON.parse(form.pros) : []"
                @update="(value) => (form.pros = value)"
            />
            <PrimeVueFormError v-if="$field?.invalid" :errors="$field.errors" />
        </FormField>

        <!-- Cons Field using ListInput -->
        <FormField v-slot="$field" name="cons" class="flex flex-col gap-1">
            <label class="block text-sm font-medium text-gray-700">Cons</label>
            <ListInput
                :maxSize="10"
                :initialValues="form.cons ? JSON.parse(form.cons) : []"
                @update="(value) => (form.cons = value)"
            />
            <PrimeVueFormError v-if="$field?.invalid" :errors="$field.errors" />
        </FormField>

        <!-- Submit Button -->
        <Button
            type="submit"
            label="Submit Review"
            class="mt-4 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600"
        />
    </Form>
</template>
