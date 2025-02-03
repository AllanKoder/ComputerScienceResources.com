<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Button from "primevue/button";
import { Stepper, StepList, Step, StepPanel, StepPanels } from "primevue";

import MandatoryFields from "@/Pages/Resources/Form/MandatoryFields.vue";
import TagSelector from "@/Components/Form/TagSelector.vue";
import Message from "primevue/message";
import TopicsFields from "./Form/TopicsFields.vue";

const form = useForm("CreateResource", {
    name: "test",
    formats: ["website"],
    url: "http://youtube.com",
    pricing: "free",
    difficulty: "academic",
    description: "http://youtube.com",
    imageUrl: "http://youtube.com",
    languages: [],
    topics: [],
});

const submitForm = () => {
    form.post("/resources");
};

const handleFormChange = (newFormData) => {
    Object.keys(newFormData).forEach((key) => {
        form[key] = newFormData[key];
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
                                :form="form"
                                @change="handleFormChange"
                                @next="activateCallback('2')"
                            ></MandatoryFields>
                        </StepPanel>
                        <StepPanel v-slot="{ activateCallback }" value="2">
                            <TopicsFields
                                :form="form"
                                @change="handleFormChange"
                                @back="activateCallback('1')"
                                @next="activateCallback('3')"
                            ></TopicsFields>
                        </StepPanel>
                        <StepPanel v-slot="{ activateCallback }" value="3">
                            <div class="flex flex-col">
                                <TagSelector
                                    :form="form"
                                    @changed="(tags) => (form.languages = tags)"
                                ></TagSelector>
                            </div>

                            <Button
                                label="Back"
                                severity="secondary"
                                icon="pi pi-arrow-left"
                                @click="activateCallback('2')"
                            />

                            <Button
                                label="Submit"
                                class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded w-full"
                                @click="submitForm"
                            />
                        </StepPanel>
                    </StepPanels>
                </Stepper>
            </div>
        </main>
    </AppLayout>
</template>
