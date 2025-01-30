<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import MultiSelect from "primevue/multiselect";
import Select from "primevue/select";

const resourceTypes = [
    "Youtube", "Book", "Podcast", "Videos", "Blog", "Website", "Newsletter", "Forum", 
    "Mobile app", "Bootcamp", "Workshop", "Course", "Desktop app", "E-zine"
];

const pricingOptions = ["Free", "Paid", "Freemium"];
const difficultyLevels = [
    "Beginners", "Industry Simple", "Industry Standard", 
    "Industry Professional", "Academic"
];

const form = useForm('CreateResource', {
    title: "",
    type: [],
    url: "",
    pricing: "",
    difficulty: "",
    description: "",
    imageUrl: ""
});

const submitForm = () => {
    form.post("/resources");
};
</script>

<template>
    <AppLayout title="Computer Science Resources">
        <main class="py-12">
            <div class="max-w-6xl mx-auto px-6">
                <h2 class="text-2xl font-bold mb-4">Add New Resource</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <InputText v-model="form.title" placeholder="Title" class="p-inputtext w-full" />
                    <MultiSelect v-model="form.type" :options="resourceTypes" placeholder="Select Types" class="w-full" :filter="true" />
                    <InputText v-model="form.url" placeholder="Resource URL" class="p-inputtext w-full" />
                    <Select v-model="form.pricing" :options="pricingOptions" placeholder="Pricing" class="w-full" />
                    <Select v-model="form.difficulty" :options="difficultyLevels" placeholder="Difficulty" class="w-full" />
                    <InputText v-model="form.imageUrl" placeholder="Image URL" class="p-inputtext w-full" />
                    <Textarea v-model="form.description" placeholder="Description" class="p-inputtext w-full" rows="3" />
                </div>
                <Button label="Submit" class="mt-4" @click="submitForm" />
            </div>
        </main>
    </AppLayout>
</template>
