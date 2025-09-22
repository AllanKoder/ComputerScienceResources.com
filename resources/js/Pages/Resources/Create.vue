<script setup>
import { ref, reactive } from "vue";
import { useToast } from "primevue/usetoast";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Stepper, StepList, Step, StepPanel, StepPanels } from "primevue";
import { router } from "@inertiajs/vue3";

import MandatoryFields from "@/Pages/Resources/Form/MandatoryFields.vue";
import TagsFields from "@/Pages/Resources/Form/TagsFields.vue";
import { useLocalStorageSaver } from "@/Composables/useLocalStorageSaver";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SubmissionGuidelines from "../../Components/Resources/SubmissionGuidelines.vue";
import FormSaverChip from "@/Components/Form/FormSaverChip.vue";

const formFields = [
    "name",
    "platforms",
    "page_url",
    "image_file",
    "pricing",
    "difficulties",
    "description",
    "topic_tags",
    "programming_language_tags",
    "general_tags",
];

const formData = reactive({
    name: "",
    platforms: [],
    page_url: "",
    image_file: null,
    pricing: "",
    difficulties: [],
    description: "",
    topic_tags: [],
    programming_language_tags: [],
    general_tags: [],
});

const { isSavedToLocalStorage, hasFormContent, clearLocalStorage } =
    useLocalStorageSaver(formData, "new-resource", formFields, "create-draft");

const showReset = ref(false);
const stepperValue = ref("1");
const formRef = ref(null);
const isLoading = ref(false);
const toast = useToast();

const scrollToForm = () => {
    if (formRef.value) {
        formRef.value.scrollIntoView({
            behavior: "smooth",
            block: "start",
            inline: "nearest",
        });
    }
};

const navigateToStep = (step) => {
    stepperValue.value = step;
    scrollToForm();
};

const resetFormData = () => {
    formData.name = "";
    formData.platforms = [];
    formData.page_url = "";
    formData.image_file = null;
    formData.pricing = "";
    formData.difficulties = [];
    formData.description = "";
    formData.topic_tags = [];
    formData.programming_language_tags = [];
    formData.general_tags = [];
};

// Update resetForm to also clear the reactive formData
const resetForm = () => {
    clearLocalStorage();
    resetFormData();
    showReset.value = false;
    stepperValue.value = "1";
};

const submitForm = async () => {
    isLoading.value = true;
    try {
        const response = await axios.postForm(
            route("resources.store"),
            formData
        );

        clearLocalStorage();
        isLoading.value = false;
        router.visit(route("resources.show", { slug: response.data.slug }));
    } catch (err) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail:
                err.response?.data?.message ||
                (err.response?.data?.errors
                    ? Object.values(err.response.data.errors).flat().join("\n")
                    : "An error occurred while creating the resource. Please try again."),
            life: 10000,
        });
        isLoading.value = false;
        console.error(err);
    }
};

const handleFormChange = (newFormData) => {
    Object.keys(newFormData).forEach((key) => {
        if (key === "page_url" && newFormData[key]) {
            if (!/^https?:\/\//i.test(newFormData[key])) {
                formData[key] = "https://" + newFormData[key];
            } else {
                formData[key] = newFormData[key];
            }
        } else {
            formData[key] = newFormData[key];
        }
    });
};
</script>

<template>
    <AppLayout title="Computer Science Resources">
        <main class="py-12 flex justify-center">
            <div
                class="w-full flex flex-col md:flex-row gap-10 justify-center px-6 items-start"
            >
                <!-- Instructions Sidebar (Rules) -->
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 w-full md:w-[28vw] min-w-[20rem] h-fit sticky md:top-8 mt-0 md:mb-0"
                >
                    <SubmissionGuidelines />
                </div>
                <!-- Main Form Section -->
                <div
                    ref="formRef"
                    class="bg-white dark:bg-gray-900 h-min shadow-lg rounded-lg p-5 relative overflow-auto w-full md:w-[46vw] max-w-4xl border border-transparent dark:border-gray-800"
                    id="create-resource-form"
                >
                    <div class="flex justify-between mb-2">
                        <FormSaverChip
                            :is-saved="isSavedToLocalStorage"
                            :has-content="hasFormContent"
                        />

                        <SecondaryButton
                            @click="showReset = true"
                            type="button"
                        >
                            Clear All
                        </SecondaryButton>
                    </div>
                    <Stepper :value="stepperValue" linear>
                        <h2 class="text-2xl font-bold mb-4 text-center">
                            Add a New Resource
                        </h2>
                        <StepList>
                            <Step value="1">Resource Details</Step>
                            <Step value="2">Tags and Classification</Step>
                        </StepList>
                        <StepPanels>
                            <StepPanel value="1">
                                <MandatoryFields
                                    :formData="formData"
                                    @change="handleFormChange"
                                    @next="() => navigateToStep('2')"
                                ></MandatoryFields>
                            </StepPanel>
                            <StepPanel value="2">
                                <TagsFields
                                    :form="formData"
                                    @change="handleFormChange"
                                    @back="() => navigateToStep('1')"
                                    @next="submitForm"
                                    :is-loading="isLoading"
                                />
                            </StepPanel>
                        </StepPanels>
                    </Stepper>

                    <ConfirmationModal
                        :show="showReset"
                        @close="showReset = false"
                    >
                        <template #title> Reset the form </template>

                        <template #content>
                            Are you sure you want reset your fields for this
                            potential new resource? You will lose your saved
                            changes.
                        </template>

                        <template #footer>
                            <SecondaryButton @click="showReset = false">
                                Cancel
                            </SecondaryButton>

                            <DangerButton
                                class="ms-3"
                                :class="{
                                    'opacity-25': formData.processing,
                                }"
                                :disabled="formData.processing"
                                @click="resetForm"
                            >
                                Reset Form
                            </DangerButton>
                        </template>
                    </ConfirmationModal>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
