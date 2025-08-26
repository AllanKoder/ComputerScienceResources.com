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
                <div
                    class="bg-gray-50 border border-gray-200 rounded-lg p-6 w-full md:w-[28vw] min-w-[20rem] h-fit sticky md:top-8 mt-0 md:mb-0"
                >
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        Submission Guidelines
                    </h3>
                    <div class="space-y-4 text-sm text-gray-700">
                        <div class="space-y-4 text-sm text-gray-700">
                            <div>
                                <h4 class="font-bold text-gray-800 mb-2">
                                    Resource Scope
                                </h4>
                                <p class="mb-2">
                                    This site features comprehensive learning
                                    resources rather than isolated materials.
                                    Resources should provide ongoing value or
                                    structured learning experiences rather than
                                    a single-use reference.
                                </p>
                                <p>
                                    Our goal is to provide ways for developers
                                    to hone their skills. This industry is
                                    filled with passion, so we should make it
                                    easier to find more ways to learn.
                                </p>
                            </div>

                            <div>
                                <h4 class="font-bold text-gray-800 mb-2">
                                    Types of Content Accepted
                                </h4>
                                <ul
                                    class="list-disc list-inside space-y-1 text-xs"
                                >
                                    <li>
                                        Platforms, websites, and tools that
                                        offer interactive learning
                                    </li>
                                    <li>
                                        Collections of educational content, such
                                        as YouTube channels or book series
                                    </li>
                                    <li>
                                        Guides or repositories that serve as
                                        long-term learning hubs
                                    </li>
                                    <li>
                                        Newsletters that consistently release
                                        content to date
                                    </li>
                                    <li>
                                        Organizations that can provide software
                                        career advising
                                    </li>
                                </ul>
                                <p class="mt-2 text-xs">
                                    Anything to help people learn more about
                                    software: from hardware, system design, to
                                    project management. The more specialized the
                                    resources are, the better.
                                </p>
                            </div>

                            <div>
                                <h4 class="font-bold text-gray-800 mb-2">
                                    Exceptions
                                </h4>
                                <ul
                                    class="list-disc list-inside space-y-1 text-xs"
                                >
                                    <li>
                                        An individual book may be included if it
                                        is exceptionally well-regarded and
                                        widely recommended as a foundational
                                        resource
                                    </li>
                                    <li>
                                        Entertainment streamers and YouTubers
                                        can be included given that they are very
                                        popular whilst still informative
                                    </li>
                                    <li>
                                        Do not post your paid courses unless
                                        they are well received - this is not a
                                        platform to advertise unwanted courses
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <h4 class="font-bold text-gray-800 mb-2">
                                    What's Not Included
                                </h4>
                                <ul
                                    class="list-disc list-inside space-y-1 text-xs"
                                >
                                    <li>
                                        Standalone videos, single blog posts, or
                                        one-off articles
                                    </li>
                                    <li>
                                        Resources that are too broad and do not
                                        contain a singular focus
                                    </li>
                                    <li>
                                        Things not related to learning about
                                        computer science or software engineering
                                    </li>
                                    <li>
                                        Lifestyle or personal finance content
                                        (beyond reasonable project management)
                                    </li>
                                </ul>
                                <p class="mt-2 text-xs italic">
                                    In the end, we trust you to be reasonable.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Main Form Section -->
                <div
                    ref="formRef"
                    class="bg-white h-min shadow-lg rounded-lg p-6 flex-1 max-w-full md:max-w-3xl min-w-0 md:min-w-[28rem] relative"
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
