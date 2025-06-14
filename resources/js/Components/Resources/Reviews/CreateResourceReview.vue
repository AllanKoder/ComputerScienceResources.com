<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { useForm } from "@inertiajs/vue3";

import { Form, FormField } from "@primevue/forms";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";
import InputText from "primevue/inputtext";
import InputTextarea from "primevue/textarea";
import Rating from "primevue/rating";
import ListInput from "@/Components/ListInput.vue";
import Button from "primevue/button";

import { yupResolver } from "@primevue/forms/resolvers/yup";
import { resourceReviewFields } from "@/Helpers/validation";

const props = defineProps({
    resourceId: {
        type: Number,
        required: true,
    },
});

const form = useForm({
    title: "",
    description: "",
    community: null,
    teaching_clarity: null,
    engagement: null,
    practicality: null,
    user_friendliness: null,
    updates: null,
    pros: [],
    cons: [],
});

const resolver = ref(yupResolver(resourceReviewFields));
const isSavedToLocalStorage = ref(false);
const isSubmitting = ref(false);

// LocalStorage key for this resource's review draft
const localStorageKey = computed(() => `review-draft-${props.resourceId}`);

// Check if form has any content
const hasFormContent = computed(() => {
    return (
        form.title.trim() !== "" ||
        form.description.trim() !== "" ||
        form.community !== null ||
        form.teaching_clarity !== null ||
        form.engagement !== null ||
        form.practicality !== null ||
        form.user_friendliness !== null ||
        form.updates !== null ||
        form.pros.length > 0 ||
        form.cons.length > 0
    );
});

// Save form data to localStorage
const saveToLocalStorage = () => {
    if (hasFormContent.value) {
        const formData = {
            title: form.title,
            description: form.description,
            community: form.community,
            teaching_clarity: form.teaching_clarity,
            engagement: form.engagement,
            practicality: form.practicality,
            user_friendliness: form.user_friendliness,
            updates: form.updates,
            pros: form.pros,
            cons: form.cons,
            savedAt: new Date().toISOString()
        };
        localStorage.setItem(localStorageKey.value, JSON.stringify(formData));
        isSavedToLocalStorage.value = true;
    } else {
        // Remove from localStorage if form is empty
        localStorage.removeItem(localStorageKey.value);
        isSavedToLocalStorage.value = false;
    }
};

// Load form data from localStorage
const loadFromLocalStorage = () => {
    const savedData = localStorage.getItem(localStorageKey.value);
    if (savedData) {
        try {
            const parsedData = JSON.parse(savedData);
            form.title = parsedData.title || "";
            form.description = parsedData.description || "";
            form.community = parsedData.community;
            form.teaching_clarity = parsedData.teaching_clarity;
            form.engagement = parsedData.engagement;
            form.practicality = parsedData.practicality;
            form.user_friendliness = parsedData.user_friendliness;
            form.updates = parsedData.updates;
            form.pros = parsedData.pros || [];
            form.cons = parsedData.cons || [];
            isSavedToLocalStorage.value = true;
        } catch (error) {
            console.error('Error loading saved review:', error);
        }
    }
};

// Watch for changes and save to localStorage with debounce
let saveTimeout;
watch(
    () => [
        form.title,
        form.description,
        form.community,
        form.teaching_clarity,
        form.engagement,
        form.practicality,
        form.user_friendliness,
        form.updates,
        form.pros,
        form.cons,
    ],
    () => {
        if (!isSubmitting.value) {
            // Debounce the save operation
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                saveToLocalStorage();
            }, 500); // Save after 500ms of inactivity
        }
    },
    { deep: true }
);

onMounted(() => {
    loadFromLocalStorage();
});

onUnmounted(() => {
    clearTimeout(saveTimeout);
});

const submitReview = (event) => {
    if (!event.valid) {
        console.error("Validation errors");
        return;
    }

    isSubmitting.value = true;

    form.post(
        route("reviews.store", {
            computerScienceResource: props.resourceId,
        }),
        {
            preserveScroll: true,
            onFinish: () => {
                isSubmitting.value = false;
            },
            onSuccess: () => {
                // Clear localStorage on successful submission
                localStorage.removeItem(localStorageKey.value);
                isSavedToLocalStorage.value = false;
            },
            onError: () => {
                isSubmitting.value = false;
            }
        }
    );
};
</script>

<template>
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-6 relative">
        <!-- Saved to localStorage indicator -->
        <div
            v-if="isSavedToLocalStorage && hasFormContent"
            class="absolute top-4 right-4 bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium flex items-center gap-1"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Saved to local storage
        </div>

        <h2 class="text-2xl font-semibold mb-6">Write a Review</h2>

        <Form
            :resolver="resolver"
            :initialValues="form"
            @submit="submitReview"
            class="flex flex-col gap-6"
        >
            <!-- Title -->
            <FormField v-slot="$field" name="title">
                <label class="font-medium">Title</label>
                <InputText
                    v-model="form.title"
                    placeholder="Review title"
                    class="w-full rounded-md border-gray-300 mt-1"
                />
                <PrimeVueFormError
                    v-if="$field.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- Description -->
            <FormField v-slot="$field" name="description">
                <label class="font-medium">Description</label>
                <InputTextarea
                    v-model="form.description"
                    placeholder="Write your thoughts..."
                    rows="4"
                    class="w-full rounded-md border-gray-300 mt-1"
                />
                <PrimeVueFormError
                    v-if="$field.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- Ratings (flex layout like your review page) -->
            <div class="flex flex-wrap gap-6 mt-4">
                <FormField
                    v-slot="$field"
                    name="community"
                    class="flex flex-col items-center"
                >
                    <span class="font-semibold mb-1">Community</span>
                    <Rating v-model="form.community" :cancel="false" />
                    <PrimeVueFormError
                        v-if="$field.invalid"
                        :errors="$field.errors"
                    />
                </FormField>

                <FormField
                    v-slot="$field"
                    name="teaching_clarity"
                    class="flex flex-col items-center"
                >
                    <span class="font-semibold mb-1">Teaching Clarity</span>
                    <Rating v-model="form.teaching_clarity" :cancel="false" />
                    <PrimeVueFormError
                        v-if="$field.invalid"
                        :errors="$field.errors"
                    />
                </FormField>

                <FormField
                    v-slot="$field"
                    name="engagement"
                    class="flex flex-col items-center"
                >
                    <span class="font-semibold mb-1">Engagement</span>
                    <Rating v-model="form.engagement" :cancel="false" />
                    <PrimeVueFormError
                        v-if="$field.invalid"
                        :errors="$field.errors"
                    />
                </FormField>

                <FormField
                    v-slot="$field"
                    name="practicality"
                    class="flex flex-col items-center"
                >
                    <span class="font-semibold mb-1">Practicality</span>
                    <Rating v-model="form.practicality" :cancel="false" />
                    <PrimeVueFormError
                        v-if="$field.invalid"
                        :errors="$field.errors"
                    />
                </FormField>

                <FormField
                    v-slot="$field"
                    name="user_friendliness"
                    class="flex flex-col items-center"
                >
                    <span class="font-semibold mb-1">User Friendliness</span>
                    <Rating v-model="form.user_friendliness" :cancel="false" />
                    <PrimeVueFormError
                        v-if="$field.invalid"
                        :errors="$field.errors"
                    />
                </FormField>

                <FormField
                    v-slot="$field"
                    name="updates"
                    class="flex flex-col items-center"
                >
                    <span class="font-semibold mb-1">Updates</span>
                    <Rating v-model="form.updates" :cancel="false" />
                    <PrimeVueFormError
                        v-if="$field.invalid"
                        :errors="$field.errors"
                    />
                </FormField>
            </div>

            <!-- Pros and Cons side by side -->
            <div class="flex flex-col md:flex-row gap-6 mt-6">
                <FormField v-slot="$field" name="pros" class="flex-1">
                    <label class="font-semibold">Pros</label>
                    <ListInput
                        :maxSize="10"
                        :initialValues="form.pros"
                        @change="(val) => (form.pros = val)"
                    />
                    <PrimeVueFormError
                        v-if="$field.invalid"
                        :errors="$field.errors"
                    />
                </FormField>

                <FormField v-slot="$field" name="cons" class="flex-1">
                    <label class="font-semibold">Cons</label>
                    <ListInput
                        :maxSize="10"
                        :initialValues="form.cons"
                        @change="(val) => (form.cons = val)"
                    />
                    <PrimeVueFormError
                        v-if="$field.invalid"
                        :errors="$field.errors"
                    />
                </FormField>
            </div>

            <!-- Submit -->
            <div class="text-right">
                <Button
                    type="submit"
                    label="Submit Review"
                    class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-6 py-2 mt-4"
                />
            </div>
        </Form>
    </div>
</template>
