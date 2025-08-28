<script setup>
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import MultiSelect from "primevue/multiselect";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";
import PictureInput from "vue-picture-input";
import Select from "primevue/select";
import { defineEmits, ref, watch } from "vue";
import {
    platformsObject,
    pricingsObject,
    difficultiesObject,
} from "@/Helpers/labels";
import { resourceMandatoryFields } from "@/Helpers/validation";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Icon } from "@iconify/vue";

const props = defineProps({
    formData: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["change", "next"]);

const errors = ref({});

function onImageChange(event) {
    const file = event.target.files[0];
    props.formData.image_file = file;
}

const validateAndNext = async () => {
    try {
        await resourceMandatoryFields.validate(props.formData, {
            abortEarly: false,
        });
        errors.value = {};
        emit("next", props.formData);
    } catch (e) {
        const yupErrors = {};
        e.inner.forEach((error) => {
            yupErrors[error.path] = error.errors;
        });
        errors.value = yupErrors;
    }
};

watch(
    () => props.formData,
    (newValue) => {
        emit("change", newValue);
    },
    { deep: true }
);
</script>

<template>
    <div class="flex flex-col gap-4 w-full">
        <div class="space-y-4">
            <!-- Name Field -->
            <div class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700"
                    >Name
                    <span class="text-red-500"> * </span>
                </label>

                <InputText
                    v-model="props.formData.name"
                    placeholder="Enter the Name"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="errors.name"
                    :errors="errors.name"
                />
            </div>

            <!-- URL Field -->
            <div class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700"
                    >Resource Website URL
                    <span class="text-red-500"> * </span>
                </label>
                <InputText
                    v-model="props.formData.page_url"
                    placeholder="Enter resource URL"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="errors.page_url"
                    :errors="errors.page_url"
                />
            </div>

            <!-- Image URL Field -->
            <div class="flex flex-col gap-1">
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
                    :prefill="props.formData.image_url"
                    @change="onImageChange"
                />
                <PrimeVueFormError
                    v-if="errors.image_file"
                    :errors="errors.image_file"
                />
            </div>

            <div class="flex flex-col gap-1">
                <!-- Resource Platforms Field -->
                <label class="block text-sm font-medium text-gray-700"
                    >Resource Platforms
                    <span class="text-red-500"> * </span>
                </label>
                <MultiSelect
                    v-model="props.formData.platforms"
                    :options="platformsObject"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Resource Platform"
                    class="w-full"
                />
                <PrimeVueFormError
                    v-if="errors.platforms"
                    :errors="errors.platforms"
                />
            </div>

            <!-- Description Field -->
            <div class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700"
                    >Description
                    <span class="text-red-500"> * </span>
                </label>
                <Textarea
                    v-model="props.formData.description"
                    placeholder="Describe the resource..."
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    rows="8"
                />
                <PrimeVueFormError
                    v-if="errors.description"
                    :errors="errors.description"
                />
            </div>

            <!-- Difficulty Field -->
            <div class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700"
                    >Difficulty
                    <span class="text-red-500"> * </span>
                </label>
                <Select
                    v-model="props.formData.difficulty"
                    :options="difficultiesObject"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Difficulty Level"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="errors.difficulty"
                    :errors="errors.difficulty"
                />
            </div>

            <!-- Pricing Field -->
            <div class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700"
                    >Pricing
                    <span class="text-red-500"> * </span>
                </label>
                <Select
                    v-model="props.formData.pricing"
                    :options="pricingsObject"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Pricing"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                />
                <PrimeVueFormError
                    v-if="errors.pricing"
                    :errors="errors.pricing"
                />
            </div>
        </div>
        <!-- continue Button -->
        <div class="flex pt-6 justify-end">
            <PrimaryButton
                @click="validateAndNext"
            >
                Next
                <Icon class="ml-2" icon="mdi:arrow-right"/>
            </PrimaryButton>
        </div>
    </div>
</template>
