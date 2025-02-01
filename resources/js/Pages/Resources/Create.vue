<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Button from "primevue/button";
import { Stepper, StepList, Step, StepPanel, StepPanels } from "primevue";

import MandatoryFields from "@/Pages/Resources/Form/MandatoryFields.vue";
import TagsField from "./Form/TagsField.vue";

const form = useForm("CreateResource", {
    name: "",
    formats: [],
    url: "",
    pricing: "",
    difficulty: "",
    description: "",
    imageUrl: "",
});

const submitForm = () => {
    form.post("/resources");
};

const handleFormChange = (newFormData) => {
    Object.keys(newFormData).forEach(key => {
        form[key] = newFormData[key];
    });

    console.log(form);
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
                        <Step value="1">Header I</Step>
                        <Step value="2">Header II</Step>
                        <Step value="3">Header III</Step>
                    </StepList>
                    <StepPanels>
                        <StepPanel v-slot="{ activateCallback }" value="1">
                            <MandatoryFields
                                :form="form"
                                @change="handleFormChange"
                            ></MandatoryFields>

                            <div class="flex pt-6 justify-end">
                                <Button
                                    label="Next"
                                    icon="pi pi-arrow-right"
                                    @click="activateCallback('2')"
                                />
                            </div>
                        </StepPanel>
                        <StepPanel v-slot="{ activateCallback }" value="2">
                            <div class="flex flex-col">
                                <h2 class="text-2xl font-bold mb-4 text-center">
                                    List the Programming Languages involved (if
                                    any)
                                </h2>

                                <TagsField
                                    :form="form"
                                    field="languages"
                                    @change="handleFormChange"
                                ></TagsField>
                            </div>
                            <div class="flex pt-6 justify-between">
                                <Button
                                    label="Back"
                                    severity="secondary"
                                    icon="pi pi-arrow-left"
                                    @click="activateCallback('1')"
                                />
                                <Button
                                    label="Next"
                                    icon="pi pi-arrow-right"
                                    iconPos="right"
                                    @click="activateCallback('3')"
                                />
                            </div>
                        </StepPanel>
                        <StepPanel v-slot="{ activateCallback }" value="3">
                            <div class="flex flex-col h-48">
                                <div
                                    class="border-2 border-dashed border-surface-200 dark:border-surface-700 rounded bg-surface-50 dark:bg-surface-950 flex-auto flex justify-center items-center font-medium"
                                >
                                    Content III
                                </div>
                            </div>
                            <div class="pt-6">
                                <Button
                                    label="Back"
                                    severity="secondary"
                                    icon="pi pi-arrow-left"
                                    @click="activateCallback('2')"
                                />
                            </div>

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
