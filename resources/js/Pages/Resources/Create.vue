<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Stepper, StepList, Step, StepPanel, StepPanels } from "primevue";

import MandatoryFields from "@/Pages/Resources/Form/MandatoryFields.vue";
import TagsFields from "@/Pages/Resources/Form/TagsFields.vue";
import TopicsFields from "@/Pages/Resources/Form/TopicsFields.vue";
import { useLocalStorageSaver } from "@/Composables/useLocalStorageSaver";

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
    "new",
    formFields,
    "create-draft"
);

const submitForm = () => {
    formData.post(route("resources.store"), {
        onSuccess: () => {
            clearLocalStorage();
            formData.reset();
        },
        onError: (errors) => {
            console.error("Errors:", errors);
            // Handle errors display them to the user
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
            <div
                class="bg-white shadow-lg rounded-lg p-6 max-w-screen-md w-full"
            >
                <Stepper value="1" linear>
                    <h2 class="text-2xl font-bold mb-4 text-center">
                        Add a New Resource
                    </h2>
                    <StepList>
                        <Step value="1">Details</Step>
                        <Step value="2">Topics</Step>
                        <Step value="3">Tags</Step>
                    </StepList>
                    <StepPanels>
                        <StepPanel v-slot="{ activateCallback }" value="1">
                            <MandatoryFields
                                :formData="formData"
                                @change="handleFormChange"
                                @next="activateCallback('2')"
                            ></MandatoryFields>
                        </StepPanel>
                        <StepPanel v-slot="{ activateCallback }" value="2">
                            <TopicsFields
                                :form="formData"
                                @change="handleFormChange"
                                @back="activateCallback('1')"
                                @next="activateCallback('3')"
                            ></TopicsFields>
                        </StepPanel>
                        <StepPanel v-slot="{ activateCallback }" value="3">
                            <div class="flex flex-col">
                                <TagsFields
                                    :form="formData"
                                    @change="handleFormChange"
                                    @back="activateCallback('2')"
                                    @next="submitForm"
                                />
                            </div>
                        </StepPanel>
                    </StepPanels>
                </Stepper>
            </div>
        </main>
    </AppLayout>
</template>
