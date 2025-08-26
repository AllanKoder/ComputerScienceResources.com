<script setup>
import { ref } from "vue";
import { useToast } from "primevue/usetoast";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Stepper, StepList, Step, StepPanel, StepPanels } from "primevue";

import MandatoryFields from "@/Pages/Resources/Form/MandatoryFields.vue";
import TagsFields from "@/Pages/Resources/Form/TagsFields.vue";
import TopicsFields from "@/Pages/Resources/Form/TopicsFields.vue";
import { useLocalStorageSaver } from "@/Composables/useLocalStorageSaver";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SubmissionGuidelines from '../../Components/SubmissionGuidelines.vue'

const formFields = [
    "name",
    "platforms",
    "page_url",
    "image_file",
    "pricing",
    "difficulty",
    "description",
    "topic_tags",
    "programming_language_tags",
    "general_tags",
];

const formData = useForm("CreateResource", {
    name: "",
    platforms: [],
    page_url: "",
    image_file: null,
    pricing: "",
    difficulty: "",
    description: "",
    topic_tags: [],
    programming_language_tags: [],
    general_tags: [],
});

const { clearLocalStorage } = useLocalStorageSaver(
    formData,
    "new-resource",
    formFields,
    "create-draft"
);

const showReset = ref(false);
const stepperValue = ref("1");
const formRef = ref(null);

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

const resetForm = () => {
    clearLocalStorage();
    formData.reset();

    showReset.value = false;
    stepperValue.value = "1";
};

const submitForm = () => {
    formData.post(route("resources.store"), {
        onSuccess: () => {
            clearLocalStorage();
            formData.reset();
        },
        onError: (errors) => {
            console.error("Errors:", errors);
            toast.add({
                severity: "error",
                summary: "Error",
                detail: errors
                    ? Object.values(errors).flat().join("\n")
                    : "An error occurred while creating the resource.",
                life: 10000,
            });
        },
    });
};

const handleFormChange = (newFormData) => {
    Object.keys(newFormData).forEach((key) => {
        formData[key] = newFormData[key];
    });
};
</script>

<template>
    <AppLayout title="Computer Science Resources">
        <main class="py-12 flex justify-center">
            <div class="w-full flex flex-col md:flex-row gap-10 justify-center items-start">
                <!-- Instructions Sidebar (Rules) -->
                <SubmissionGuidelines />
                <!-- Main Form Section -->
                <div
                    ref="formRef"
                    class="bg-white h-min shadow-lg rounded-lg p-5 relative overflow-auto w-full md:w-[46vw] max-w-4xl min-w-[28rem]"
                    id="create-resource-form"
                >
                    <!-- Move Reset button to top right -->
                    <div class="flex justify-end mb-2">
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
                            <Step value="1">Details</Step>
                            <Step value="2">Topics</Step>
                            <Step value="3">Tags</Step>
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
                                <TopicsFields
                                    :form="formData"
                                    @change="handleFormChange"
                                    @back="() => navigateToStep('1')"
                                    @next="() => navigateToStep('3')"
                                ></TopicsFields>
                            </StepPanel>
                            <StepPanel value="3">
                                <TagsFields
                                    :form="formData"
                                    @change="handleFormChange"
                                    @back="() => navigateToStep('2')"
                                    @next="submitForm"
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
