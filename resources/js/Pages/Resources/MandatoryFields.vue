<script setup>
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import MultiSelect from "primevue/multiselect";
import PrimeVueFormError from "@/Components/Form/PrimeVueFormError.vue";
import PictureInput from "vue-picture-input";
import Select from "primevue/select";
import { defineEmits, ref, watch } from "vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
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
const showDescriptionHelp = ref(false);

function onImageChange(event) {
    const file = event.target.files[0];
    props.formData.image_file = file;
}

function onImageRemove(event) {
    props.formData.image_file = null;
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >Name
                    <span class="text-red-500"> * </span>
                </label>

                <InputText
                    v-model="props.formData.name"
                    placeholder="Enter the Name"
                    class="mt-1 w-full border border-gray-300 dark:border-gray-800 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:text-gray-100"
                />
                <PrimeVueFormError
                    v-if="errors.name"
                    :errors="errors.name"
                />
            </div>

            <!-- URL Field -->
            <div class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >Resource Website URL
                    <span class="text-red-500"> * </span>
                </label>
                <InputText
                    v-model="props.formData.page_url"
                    placeholder="Enter resource URL"
                    class="mt-1 w-full border border-gray-300 dark:border-gray-800 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-900 dark:text-gray-100"
                />
                <PrimeVueFormError
                    v-if="errors.page_url"
                    :errors="errors.page_url"
                />
            </div>

            <!-- Image URL Field -->
            <div class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >Image Thumbnail</label
                >
                <div class="mt-1 w-fit border border-gray-300 dark:border-gray-800 rounded-md bg-white dark:bg-gray-900 p-3 flex items-center justify-center">
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
                        :prefill="props.formData.image_file"
                        @change="onImageChange"
                        @remove="onImageRemove"
                    />
                </div>
                <PrimeVueFormError
                    v-if="errors.image_file"
                    :errors="errors.image_file"
                />
            </div>

            <div class="flex flex-col gap-1">
                <!-- Resource Platforms Field -->
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >Resource Platforms
                    <span class="text-red-500"> * </span>
                </label>
                <MultiSelect
                    v-model="props.formData.platforms"
                    :options="platformsObject"
                    filter
                    option-label="label"
                    option-value="value"
                    placeholder="Select Resource Platform"
                    class="w-full dark:bg-gray-900 dark:text-gray-100 dark:border-gray-800"
                />
                <PrimeVueFormError
                    v-if="errors.platforms"
                    :errors="errors.platforms"
                />
            </div>

            <!-- Description Field -->
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        Description
                        <span class="text-red-500"> * </span>
                    </label>
                    <button type="button" @click="showDescriptionHelp = true" class="focus:outline-none" title="Help: What makes a good description?">
                        <Icon icon="mdi:help-circle-outline" class="w-5 h-5 text-primary hover:text-primaryDark" />
                    </button>
                </div>
                <Textarea
                    v-model="props.formData.description"
                    :placeholder="`${props.formData.name || 'Resource Name'} is a...`"
                    class="mt-1 w-full border border-gray-300 dark:border-gray-800 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-900 dark:text-gray-100"
                    rows="8"
                />
                <PrimeVueFormError
                    v-if="errors.description"
                    :errors="errors.description"
                />
                <ConfirmationModal :show="showDescriptionHelp" @close="showDescriptionHelp = false">
                    <template #title>
                        What makes a good resource description?
                    </template>
                    <template #content>
                        <div class="space-y-3">
                            <p>
                                <span class="font-semibold">We want details!</span> A good description should clearly explain what the resource is, who it's for, and what makes it valuable. Mention the format (podcast, platform, channel, etc.), the main topics covered, and any unique features or strengths. Imagine you're helping someone decide if this resource is right for them.
                            </p>
                            <div class="border-l-4 border-primary pl-4 py-2 bg-secondary">
                                <div class="font-semibold mb-1">Example 1:</div>
                                <div class="text-xs text-gray-700">
                                    Soft Skills Engineering is a weekly advice podcast specifically designed for software developers who want to navigate the non-technical challenges of their careers. Hosted by experienced developers Dave Smith and Jamison Dance, the show addresses the interpersonal and professional situations that coding bootcamps and computer science programs typically don't cover.<br><br>
                                    The podcast tackles practical workplace scenarios that software engineers encounter regularly, including salary negotiations, managing difficult colleagues, advancing into technical leadership roles, handling code review feedback diplomatically, and making strategic career decisions like when to change jobs or seek promotions. Episodes feature listener-submitted questions covering situations ranging from dealing with underperforming team members to managing the transition into management roles.
                                </div>
                            </div>
                            <div class="border-l-4 border-primary pl-4 py-2 bg-secondary">
                                <div class="font-semibold mb-1">Example 2:</div>
                                <div class="text-xs text-gray-700">
                                    NeetCode is an online platform designed for coding interview preparation, particularly for FAANG and big tech companies. The platform provides a structured approach to preparing for coding interviews with curated problem sets and comprehensive learning resources.<br><br>
                                    The platform's flagship offering is the NeetCode 150, which expands on the popular Blind 75 problem set by adding 75 additional problems, creating a comprehensive list for developers familiar with basic algorithms and data structures. The site includes video explanations, coding solutions, and systematic approaches to tackling technical interview questions across various difficulty levels and problem categories.<br><br>
                                    NeetCode also maintains a popular YouTube channel known for its concise and straightforward explanations of common interview questions, with clear problem-solving strategies that are accessible to both beginners and experienced coders.
                                </div>
                            </div>
                        </div>
                    </template>
                    <template #footer>
                        <button @click="showDescriptionHelp = false" class="px-4 py-2 bg-primary text-white rounded hover:bg-primaryDark transition">Close</button>
                    </template>
                </ConfirmationModal>
            </div>

            <!-- Difficulty Field -->
            <div class="flex flex-col gap-1 ">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >Difficulty
                    <span class="text-red-500"> * </span>
                </label>
                <MultiSelect
                    v-model="props.formData.difficulties"
                    :options="difficultiesObject"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Difficulty Level"
                    class="mt-1 w-full border border-gray-300 dark:border-gray-800 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-900 dark:text-gray-100"
                />
                <PrimeVueFormError
                    v-if="errors.difficulties"
                    :errors="errors.difficulties"
                />
            </div>

            <!-- Pricing Field -->
            <div class="flex flex-col gap-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >Pricing
                    <span class="text-red-500"> * </span>
                </label>
                <Select
                    v-model="props.formData.pricing"
                    :options="pricingsObject"
                    option-label="label"
                    option-value="value"
                    placeholder="Select Pricing"
                    class="mt-1 w-full border border-gray-300 dark:border-gray-800 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-900 dark:text-gray-100"
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
