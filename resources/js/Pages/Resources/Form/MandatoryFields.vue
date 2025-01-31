<script setup>
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import MultiSelect from "primevue/multiselect";
import { Form } from "@primevue/forms";
import { Message } from "primevue";

import Select from "primevue/select";
import { defineProps, defineEmits } from "vue";
import { watch } from "vue";
import {
    resourceTypes,
    pricingOptions,
    difficultyLevels,
} from "@/Helpers/constants";

const props = defineProps({
    form: Object,
});

const emit = defineEmits(["change"]);

// Watch for changes in form data and emit change event if needed
watch(
    () => props.form,
    (newValue) => {
        emit("change", newValue);
    },
    { deep: true }
);
</script>

<template>
    <Form
        :resolver="undefined"
        @submit="() => console.log('test')"
        class="flex flex-col gap-4 w-full"
    >
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700"
                    >Title</label
                >
                <InputText
                    v-model="form.title"
                    placeholder="Enter the title"
                    class="mt-1 p-inputtext w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <Message
                    v-if="form.title"
                    severity="error"
                    size="small"
                    variant="simple"
                    >{{ form.title }}</Message
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700"
                    >Resource Website URL</label
                >
                <InputText
                    v-model="form.url"
                    placeholder="Enter resource URL"
                    class="mt-1 p-inputtext w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700"
                    >Image URL</label
                >
                <InputText
                    v-model="form.imageUrl"
                    placeholder="Enter image URL"
                    class="mt-1 p-inputtext w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
            </div>

            <!-- Displaying the Image Preview -->
            <div v-if="form.imageUrl" class="mt-4">
                <h3 class="text-lg font-semibold">Image Preview:</h3>
                <img
                    :src="form.imageUrl"
                    alt="Resource Image"
                    class="mt-2 border rounded shadow-md"
                    style="max-width: 100%; height: auto"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700"
                    >Resource Format</label
                >
                <MultiSelect
                    v-model="form.type"
                    :options="resourceTypes"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Types"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700"
                    >Description</label
                >
                <Textarea
                    v-model="form.description"
                    placeholder="Describe the resource and what it offers..."
                    class="mt-1 p-inputtext w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    rows="3"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700"
                    >Difficulty</label
                >
                <Select
                    v-model="form.difficulty"
                    :options="difficultyLevels"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Difficulty Level"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700"
                    >Pricing</label
                >
                <Select
                    v-model="form.pricing"
                    :options="pricingOptions"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Pricing"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
            </div>
        </div>
    </Form>
</template>
