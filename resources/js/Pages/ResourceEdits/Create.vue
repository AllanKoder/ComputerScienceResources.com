<script setup>
import { ref } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextArea from "@/Components/TextArea.vue";
import MultiSelect from "primevue/multiselect";
import Select from "primevue/select";
import TagSelector from "@/Components/Form/TagSelector.vue";
import FormSaverChip from "@/Components/Form/FormSaverChip.vue";
import { useLocalStorageSaver } from "@/Composables/useLocalStorageSaver.js";
import {
    platformsObject,
    pricingsObject,
    difficultiesObject,
} from "@/Helpers/labels";
import BackButton from "@/Components/Navigation/BackButton.vue";
import { resourceEditsFields } from "@/Helpers/validation";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import DangerButton from "@/Components/DangerButton.vue";

const showReset = ref(false);

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    edit_title: "",
    edit_description: "",
    name: props.resource.name,
    description: props.resource.description,
    page_url: props.resource.page_url,
    image_url: props.resource.image_url,
    difficulty: props.resource.difficulty,
    pricing: props.resource.pricing,
    platforms: props.resource.platforms,
    topic_tags: props.resource.topic_tags || [],
    programming_language_tags: props.resource.programming_language_tags || [],
    general_tags: props.resource.general_tags || [],
});

const formFields = [
    'edit_title',
    'edit_description',
    'name',
    'description',
    'page_url',
    'image_url',
    'difficulty',
    'pricing',
    'platforms',
    'topic_tags',
    'programming_language_tags',
    'general_tags',
];

const {
    isSavedToLocalStorage,
    isDataLoaded,
    hasFormContent,
    clearLocalStorage
} = useLocalStorageSaver(form, props.resource.id, formFields);

const resetForm = () => {
    clearLocalStorage();
    form.reset();
    showReset.value = false;
};

const submit = async () => {
    // Clear previous errors
    form.clearErrors();
    try {
        // Validate all fields
        await resourceEditsFields.validate(form, { abortEarly: false });
        // Submit if valid
        form.post(
            route("resource_edits.store", { computerScienceResource: props.resource.id }),
            { onSuccess: () => clearLocalStorage() }
        );
    } catch (error) {
        // Populate form errors from validation
        if (error.inner) {
            error.inner.forEach(err => form.setError(err.path, err.message));
        }
    }
};
</script>

<template>
    <AppLayout title="Propose an Edit">
        <Head title="Propose an Edit" />
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                v-if="isDataLoaded"
                class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 relative"
                >
                    <div class="flex-row flex my-2">
                        <BackButton :route="route('resources.show', {'computerScienceResource': props.resource.id, 'tab': 'edits'})"/>
                        <FormSaverChip :is-saved="isSavedToLocalStorage" :has-content="hasFormContent" />
                    </div>
                    <form @submit.prevent="submit">
                        <div class="mb-9 rounded-lg">
                            <h2 class="text-xl font-semibold mb-2">
                                Describe Your Change
                            </h2>
                            <div>
                                <InputLabel for="edit_title" value="Title" />
                                <TextInput
                                    id="edit_title"
                                    v-model="form.edit_title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.edit_title"
                                />
                            </div>

                            <div class="mt-4">
                                <InputLabel
                                    for="edit_description"
                                    value="Description"
                                />
                                <TextArea
                                    id="edit_description"
                                    v-model="form.edit_description"
                                    class="mt-1 block w-full"
                                    :rows="4"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.edit_description"
                                />
                            </div>
                        </div>

                        <h2 class="text-xl font-semibold mb-2">
                            New Edited Resource
                        </h2>
                        <div class="p-4 border rounded">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel
                                        for="name"
                                        value="Resource Name"
                                    />
                                    <TextInput
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.name"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="page_url" value="URL" />
                                    <TextInput
                                        id="page_url"
                                        v-model="form.page_url"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.page_url"
                                    />
                                </div>
                            </div>

                            <div class="mt-4">
                                <InputLabel
                                    for="description"
                                    value="Resource Description"
                                />
                                <TextArea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full"
                                    :rows="6"
                                    required
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.description"
                                />
                            </div>

                            <div
                                class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4"
                            >
                                <div>
                                    <InputLabel
                                        for="difficulty"
                                        value="Difficulty"
                                    />
                                    <Select
                                        id="difficulty"
                                        v-model="form.difficulty"
                                        :options="difficultiesObject"
                                        option-label="label"
                                        option-value="value"
                                        placeholder="Select Difficulty"
                                        class="w-full mt-1"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.difficulty"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="pricing" value="Pricing" />
                                    <Select
                                        id="pricing"
                                        v-model="form.pricing"
                                        :options="pricingsObject"
                                        option-label="label"
                                        option-value="value"
                                        placeholder="Select Pricing"
                                        class="w-full mt-1"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.pricing"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="platforms"
                                        value="Platforms"
                                    />
                                    <MultiSelect
                                        id="platforms"
                                        v-model="form.platforms"
                                        :options="platformsObject"
                                        option-label="label"
                                        option-value="value"
                                        placeholder="Select Platforms"
                                        class="w-full mt-1"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.platforms"
                                    />
                                </div>
                            </div>

                            <div class="mt-4">
                                <InputLabel value="Topic Tags" />
                                <TagSelector
                                    v-model="form.topic_tags"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.topic_tags"
                                />
                            </div>

                            <div class="mt-4">
                                <InputLabel value="Programming Language Tags" />
                                <TagSelector
                                    v-model="form.programming_language_tags"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="
                                        form.errors.programming_language_tags
                                    "
                                />
                            </div>

                            <div class="mt-4">
                                <InputLabel value="General Tags" />
                                <TagSelector
                                    v-model="form.general_tags"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.general_tags"
                                />
                            </div>

                            <div class="flex items-center justify-between mt-6 p-4">
                                <SecondaryButton @click="showReset = true" type="button">
                                    Reset
                                </SecondaryButton>
                                <ConfirmationModal :show="showReset" @close="showReset = false">
                                    <template #title>
                                        Reset the form
                                    </template>

                                    <template #content>
                                        Are you sure you want reset back to the default values for this resource? You will lose your saved changes.
                                    </template>

                                    <template #footer>
                                        <SecondaryButton @click="showReset = false">
                                            Cancel
                                        </SecondaryButton>

                                        <DangerButton
                                            class="ms-3"
                                            :class="{ 'opacity-25': form.processing }"
                                            :disabled="form.processing"
                                            @click="resetForm"
                                        >
                                            Reset Form
                                        </DangerButton>
                                    </template>
                                </ConfirmationModal>

                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Submit Edit
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
