<script setup>
import { ref } from "vue";
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

const submitReview = (event) => {
    if (!event.valid) {
        console.error("Validation errors");
        return;
    }

    form.post(
        route("reviews.store", {
            computerScienceResource: props.resourceId,
        }),
        { preserveScroll: true }
    );
};
</script>

<template>
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-6">
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
