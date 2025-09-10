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
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

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
const showTagsHelp = ref(false);

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
        class="flex flex-col gap-4 w-full bg-white dark:bg-gray-900 border border-transparent dark:border-gray-800 rounded-lg p-4"
    >
    <div class="flex flex-col gap-1 justify-center items-center">
            <div class="self-end">
                <button
                    type="button"
                    @click="showTagsHelp = true"
                    class="inline-flex items-center gap-1 text-sm text-primary hover:text-primaryDark focus:outline-none"
                    title="Help: What tags should I add?"
                >
                    <Icon icon="mdi:help-circle-outline" class="w-5 h-5" />
                    Tagging help
                </button>
            </div>
            <!-- TODO: MAKE THIS A COMPONENT -->
            <!-- Tag Selector for topics -->
            <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-gray-100">
                What's this resource about?
                <span class="text-red-500"> * </span>
            </h2>
            <p class="text-center italic mb-2 text-gray-600 dark:text-gray-300">
                software-engineering, career-consulting, data-science
            </p>
            <TagSelector
                :tag-type="'topics_tags'"
                v-model="props.form.topic_tags"
            ></TagSelector>
            <PrimeVueFormError :errors="errors" />

            <!-- Tag Selector for Programming Languages -->
            <h2 class="text-2xl font-bold mt-5 mb-1 text-center text-gray-900 dark:text-gray-100">
                Programming languages/frameworks taught (if any)?
            </h2>
            <p class="text-center italic mb-2 text-gray-600 dark:text-gray-300">python, c++, c#, vue.js, pytorch</p>
            <TagSelector
                :tag-type="'programming_languages_tags'"
                v-model="props.form.programming_language_tags"
            />

            <!-- Tag Selector for Other tags -->
            <h2 class="text-2xl font-bold mt-5 mb-1 text-center text-gray-900 dark:text-gray-100">
                Additional general tags
            </h2>
            <p class="text-center italic mb-2 text-gray-600 dark:text-gray-300">non-profit, open-source, funny</p>
            <TagSelector
                :tag-type="'general_tags'"
                v-model="props.form.general_tags"
            />
        </div>

        <ConfirmationModal :show="showTagsHelp" @close="showTagsHelp = false">
            <template #title>
                How to choose good tags
            </template>
            <template #content>
                <div class="space-y-4 text-sm">
                    <div>
                        <div class="font-semibold mb-1">Topic tags (required)</div>
                        <p class="text-gray-700 dark:text-gray-300">
                            Use broad, descriptive categories that capture what the resource is mainly about.
                            Avoid super niche labels here.
                        </p>
                        <ul class="list-disc pl-5 text-gray-700 dark:text-gray-300">
                            <li>Examples: software-engineering, data-science, devops, algorithms, system-design, career</li>
                        </ul>
                    </div>
                    <div>
                        <div class="font-semibold mb-1">Programming languages/frameworks</div>
                        <p class="text-gray-700 dark:text-gray-300">
                            Be specific and only include languages or frameworks the resource actively teaches or uses.
                            Please <b>don’t type "everything"</b> as a tag.
                        </p>
                        <ul class="list-disc pl-5 text-gray-700 dark:text-gray-300">
                            <li>Good: python, c++, c#, vue.js, pytorch</li>
                            <li>Avoid: "all-languages" or adding many unrelated ones</li>
                        </ul>
                    </div>
                    <div>
                        <div class="font-semibold mb-1">Other/general tags</div>
                        <p class="text-gray-700 dark:text-gray-300">
                            Everything else that helps discovery: tone, format, credentials, audience, etc.
                        </p>
                        <ul class="list-disc pl-5 text-gray-700 dark:text-gray-300">
                            <li>Examples: humour, certifications, non-profit, open-source, interview-prep</li>
                        </ul>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="showTagsHelp = false" class="px-4 py-2 bg-primary text-white rounded hover:bg-primaryDark transition">Close</button>
            </template>
        </ConfirmationModal>

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
