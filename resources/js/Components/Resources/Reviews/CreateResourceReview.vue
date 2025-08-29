<script setup>
import { ref, reactive } from "vue";

import { Form, FormField } from "@primevue/forms";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";
import InputText from "primevue/inputtext";
import InputTextarea from "primevue/textarea";
import Rating from "primevue/rating";
import ListInput from "@/Components/ListInput.vue";
import Button from "primevue/button";
import FormSaverChip from "@/Components/Form/FormSaverChip.vue";

import { resourceReviewFields } from "@/Helpers/validation";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import { useLocalStorageSaver } from "@/Composables/useLocalStorageSaver.js";

const props = defineProps({
    resourceId: {
        type: Number,
        required: true,
    },
    resourceSlug: {
        type: String,
        required: true,
    },
    isEditingMode: {
        type: Boolean,
        default: false,
    },
    resourceReview: {
        type: Object,
        default: () => null,
    },
});

const form = reactive(
    props.isEditingMode && props.resourceReview
        ? { ...props.resourceReview }
        : {
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
          }
);

const formFields = [
    "title",
    "description",
    "community",
    "teaching_clarity",
    "engagement",
    "practicality",
    "user_friendliness",
    "updates",
    "pros",
    "cons",
];

const {
    isSavedToLocalStorage,
    hasFormContent,
    clearLocalStorage,
} = useLocalStorageSaver(form, props.resourceId, formFields, "review-draft");

const isSubmitting = ref(false);
const error = ref(null);

const errors = ref({});

const submitReview = async () => {
    isSubmitting.value = true;
    try {
        await resourceReviewFields.validate(form, { abortEarly: false });
        errors.value = {};

        const url = props.isEditingMode
            ? route("reviews.update", { computerScienceResource: props.resourceId })
            : route("reviews.store", { computerScienceResource: props.resourceId });

        const method = props.isEditingMode ? "put" : "post";

        await axios[method](url, form);
        clearLocalStorage();

        const routeParams = { slug: props.resourceSlug };
        if (props.isEditingMode) {
            routeParams.tab = "reviews";
            routeParams.sort_by = "recently_updated";
        } else {
            routeParams.sort_by = "latest";
        }
        router.visit(route('resources.show', routeParams));
    } catch (e) {
        isSubmitting.value = false;
        if (e.inner) {
            const yupErrors = {};
            e.inner.forEach((error) => {
                yupErrors[error.path] = error.errors;
            });
            errors.value = yupErrors;
        } else {
            error.value = "Something went wrong with submitting your review, please refresh or try again.";
            console.error(e);
        }
    }
};
</script>

<template>
    <div
        class="mx-auto bg-white shadow-lg rounded-2xl p-6 relative"
    >
        <FormSaverChip
            :is-saved="isSavedToLocalStorage"
            :has-content="hasFormContent"
        />

        <h2 class="text-2xl font-semibold mb-6">
            {{ isEditingMode ? "Update Review" : "Write a Review" }}
        </h2>

        <Form
            :initialValues="form"
            @submit="submitReview"
            class="flex flex-col gap-6"
        >
            <!-- Title -->
            <FormField v-slot="$field" name="title">
                <label class="font-semibold">Title</label>
                <span class="text-red-500"> * </span>

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
                <label class="font-semibold">Description</label>
                <span class="text-red-500"> * </span>
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
            <div class="flex flex-wrap gap-12 mt-4 mx-auto">
                <FormField
                    v-slot="$field"
                    name="community"
                    class="flex flex-col items-center"
                >
                    <span class="font-semibold mb-1">
                        Community
                        <span class="text-red-500"> * </span>
                    </span>

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
                    <span class="font-semibold mb-1" >
                        Teaching Clarity
                        <span class="text-red-500"> * </span></span
                    >

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
                    <span class="font-semibold mb-1" >
                        Engagement <span class="text-red-500"> * </span></span
                    >

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
                    <span class="font-semibold mb-1" >
                        Practicality
                        <span class="text-red-500"> * </span></span
                    >

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
                    <span class="font-semibold mb-1" >
                        User Friendliness
                        <span class="text-red-500"> * </span></span
                    >

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
                    <span class="font-semibold mb-1" >
                        Updates <span class="text-red-500"> * </span></span
                    >

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

            <!-- Check if not auth since unauthed user will always fail (need to be logged in) -->
            <div
                v-if="error && $page.props.auth.user"
                class="text-red-500 bg-red-100 p-3 rounded-md"
            >
                {{ error }}
            </div>

            <!-- Submit -->
            <div class="text-right">
                <Button
                    type="submit"
                    :label="isEditingMode ? 'Update Review' : 'Submit Review'"
                    class="bg-primaryDark text-white rounded-lg px-6 py-2 mt-4"
                    :disabled="isSubmitting"
                />
            </div>
        </Form>
    </div>
</template>
