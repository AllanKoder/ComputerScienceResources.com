<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Button from "primevue/button";
import MandatoryFields from "@/Pages/Resources/Form/MandatoryFields.vue";

const form = useForm("CreateResource", {
    title: "",
    type: [],
    url: "",
    pricing: "",
    difficulty: "",
    description: "",
    imageUrl: "",
});

const submitForm = () => {
    form.post("/resources");
};

const handleFormChange = (newForm) => {
    // Handle form changes here
    console.log('Updated Form:', newForm);
};

</script>

<template>
    <AppLayout title="Computer Science Resources">
        <main class="py-12 flex justify-center">
            <div class="bg-white shadow-lg rounded-lg p-6 max-w-screen-md w-full">
                <h2 class="text-2xl font-bold mb-4 text-center">Add a New Resource</h2>
                <div class="space-y-4">
                    <MandatoryFields :form='form' @change="handleFormChange"></MandatoryFields>
                </div>
                <Button label="Submit" class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded w-full" @click="submitForm" />
            </div>
        </main>
    </AppLayout>
</template>

<style scoped>
/* Add any additional styles if necessary */
</style>
