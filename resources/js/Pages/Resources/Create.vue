<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Stepper, StepList, Step, StepPanel, StepPanels } from "primevue";

import MandatoryFields from "@/Pages/Resources/Form/MandatoryFields.vue";
import TagsFields from "@/Pages/Resources/Form/TagsFields.vue";
import TopicsFields from "@/Pages/Resources/Form/TopicsFields.vue";

const form = useForm("CreateResource", {
    name: "test",
    platforms: ["website"],
    pageUrl: "http://youtube.com",
    pricing: "free",
    difficulty: "academic",
    description: "http://youtube.com",
    imageUrl: "http://youtube.com",
    topics: ["te", "e", "sdf"],
    programmingLanguages: [],
});

const submitForm = () => {
    console.log(form);
    form.post(route("resources.store"), {
        onSuccess: () => console.log("yess"),
    });
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
                                <TagsFields
                                    :form="form"
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
