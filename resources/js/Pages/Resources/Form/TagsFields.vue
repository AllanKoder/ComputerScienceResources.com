<script setup>
import { ref, defineEmits, watch } from "vue";
import TagSelector from "@/Components/Form/TagSelector.vue";
import { yupResolver } from "@primevue/forms/resolvers/yup";
import { resourceMandatoryTags } from "@/Helpers/validation";
import { Form } from "@primevue/forms";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Icon } from "@iconify/vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    isLoading: {
        type: Boolean,
        required: true,
    }
});

const emit = defineEmits(["change", "next", "back"]);

const errors = ref([]);

const schema = resourceMandatoryTags;
// PrimeVue Resolver
const resolver = ref(yupResolver(schema));

// Update change
watch(
    () => props.form,
    (newValue) => {
        emit("change", newValue);
    },
    { deep: true }
);

// Function to handle form submission
const validateAndNext = async () => {
    schema
        .validate(props.form)
        .then((_) => {
            errors.value = [];
            emit("next");
        })
        .catch((error) => {
            console.error("Validation failed:", error.errors);
            errors.value = error.errors;
        });
};
</script>

<template>
    <Form
        :resolver="resolver"
        :initialValues="props.form"
        class="flex flex-col gap-4 w-full"
    >
        <div class="flex flex-col gap-1 justify-center items-center">
            <!-- TODO: MAKE THIS A COMPONENT -->
            <!-- Tag Selector for topics -->
            <h2 class="text-2xl font-bold text-center">
                What's this resource about?
                <span class="text-red-500"> * </span>
            </h2>
            <p class="text-center italic mb-2">
                software-engineering, career-consulting, data-science
            </p>
            <TagSelector
                :tag-type="'topics_tags'"
                v-model="props.form.topic_tags"
            ></TagSelector>
            <PrimeVueFormError :errors="errors" />

            <!-- Tag Selector for Programming Languages -->
            <h2 class="text-2xl font-bold mt-5 mb-1 text-center">
                Programming languages taught (if any)?
            </h2>
            <p class="text-center italic mb-2">python, c++, c#</p>
            <TagSelector
                :tag-type="'programming_languages_tags'"
                v-model="props.form.programming_language_tags"
            />

            <!-- Tag Selector for Other tags -->
            <h2 class="text-2xl font-bold mt-5 mb-1 text-center">
                Additional general tags
            </h2>
            <p class="text-center italic mb-2">non-profit, open-source, funny</p>
            <TagSelector
                :tag-type="'general_tags'"
                v-model="props.form.general_tags"
            />
        </div>

        <!-- Prev/Next Button -->
        <div class="flex pt-6 justify-between">
            <SecondaryButton @click="() => emit('back')">
                <Icon class="mr-2" icon="mdi:arrow-back" />
                Back
            </SecondaryButton>
            <PrimaryButton @click="validateAndNext" :disabled="props.isLoading">
                Submit
                <Icon class="ml-2" icon="mdi:send" />
            </PrimaryButton>
        </div>
    </Form>
</template>
