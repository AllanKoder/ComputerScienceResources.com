<script setup>
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import MultiSelect from "primevue/multiselect";
import { Button } from "primevue";
import { Form, FormField } from "@primevue/forms";
import { yupResolver } from "@primevue/forms/resolvers/yup";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";
import PictureInput from "vue-picture-input";
import Select from "primevue/select";
import { defineProps, defineEmits, ref } from "vue";
import {
    platformsObject,
    pricingsObject,
    difficultiesObject,
} from "@/Helpers/labels";
import { resourceMandatoryFields } from "@/Helpers/validation";

const props = defineProps({
    formData: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["change", "next"]);

// The validation schema
const schema = resourceMandatoryFields;
// PrimeVue Resolver
const resolver = ref(yupResolver(schema));

function onImageChange(event, field) {
    field.onInput(event.target.files[0]);
}

const validateAndNext = (formSubmitEvent) => {
    if (!formSubmitEvent.valid) return;

    emit("next", formSubmitEvent.values);
};
</script>

<template>
    <Form
        :resolver="resolver"
        :initial-values="props.formData"
        @submit="validateAndNext"
        class="flex flex-col gap-4 w-full"
    >
        <div class="space-y-4">
            <!-- Name Field -->
            <FormField v-slot="$field" name="name" class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700"
                    >Name
                    <span class="text-red-500"> * </span>
                </label>

                <InputText
                    v-bind="$field.props"
                    placeholder="Enter the Name"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- URL Field -->
            <FormField
                v-slot="$field"
                name="page_url"
                class="flex flex-col gap-1"
            >
                <label class="block text-sm font-medium text-gray-700"
                    >Resource Website URL
                    <span class="text-red-500"> * </span>
                </label>
                <InputText
                    v-bind="$field.props"
                    placeholder="Enter resource URL"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- Image URL Field -->
            <FormField
                v-slot="$field"
                name="image_file"
                class="flex flex-col gap-1"
            >
                <label class="block text-sm font-medium text-gray-700"
                    >Image Thumbnail</label
                >
                <PictureInput
                    ref="pictureInput"
                    width="220"
                    height="220"
                    margin="16"
                    accept="image/jpeg,image/png"
                    size="0.4"
                    remove-button-class="inline-flex items-center px-4 py-2 bg-primary border-0 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 focus:bg-primary/90 active:bg-primary/80 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150"
                    button-class="inline-flex items-center px-4 py-2 border border-primary rounded-md font-semibold text-xs text-primary uppercase tracking-widest hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition ease-in-out duration-150 mr-4"
                    removable
                    @change="(event) => onImageChange(event, $field)"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <FormField
                v-slot="$field"
                name="platforms"
                class="flex flex-col gap-1"
            >
                <!-- Resource Platforms Field -->
                <label class="block text-sm font-medium text-gray-700"
                    >Resource Platforms
                    <span class="text-red-500"> * </span>
                </label>
                <MultiSelect
                    v-bind="$field.props"
                    :options="platformsObject"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Resource Platform"
                    class="w-full"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- Description Field -->
            <FormField
                v-slot="$field"
                name="description"
                class="flex flex-col gap-1"
            >
                <label class="block text-sm font-medium text-gray-700"
                    >Description
                    <span class="text-red-500"> * </span>
                </label>
                <Textarea
                    v-bind="$field.props"
                    placeholder="Describe the resource..."
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    rows="3"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- Difficulty Field -->
            <FormField
                v-slot="$field"
                name="difficulty"
                class="flex flex-col gap-1"
            >
                <label class="block text-sm font-medium text-gray-700"
                    >Difficulty
                    <span class="text-red-500"> * </span>
                </label>
                <Select
                    v-bind="$field.props"
                    :options="difficultiesObject"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Difficulty Level"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>

            <!-- Pricing Field -->
            <FormField
                v-slot="$field"
                name="pricing"
                class="flex flex-col gap-1"
            >
                <label class="block text-sm font-medium text-gray-700"
                    >Pricing
                    <span class="text-red-500"> * </span>
                </label>
                <Select
                    v-bind="$field.props"
                    :options="pricingsObject"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Pricing"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="$field?.invalid"
                    :errors="$field.errors"
                />
            </FormField>
        </div>
        <!-- continue Button -->
        <div class="flex pt-6 justify-end">
            <Button label="Next" icon="pi pi-arrow-right" type="submit" />
        </div>
    </Form>
</template>
