<script setup>
import { ref } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PictureInput from "vue-picture-input";
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
import { ValidationError } from "yup";

const showReset = ref(false);
const errors = ref({});

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
});
// TODO: MOVE TO AXIOS BECAUSE REDIRECT FROM SUCCESSES WILL NOT CLEAR STORAGE, CONFIRM WITH TEST
const formData = useForm({
    edit_title: "",
    edit_description: "",
    proposed_changes: {
        name: props.resource.name,
        description: props.resource.description,
        page_url: props.resource.page_url,
        image_file: null,
        difficulties: props.resource.difficulties,
        pricing: props.resource.pricing,
        platforms: props.resource.platforms,
        topic_tags: props.resource.topic_tags || [],
        programming_language_tags:
            props.resource.programming_language_tags || [],
        general_tags: props.resource.general_tags || [],
    },
});

const formFields = ["edit_title", "edit_description", "proposed_changes"];

const { isSavedToLocalStorage, hasFormContent, clearLocalStorage } =
    useLocalStorageSaver(
        formData,
        `edit-${props.resource.id}`,
        formFields,
        'edit-draft',
    );

const pictureKey = ref(0);
const changedPicture = ref(false);

const resetImage = () => {
    formData.proposed_changes.image_file = null;
    changedPicture.value = false;
    pictureKey.value++;
    delete errors.value["proposed_changes.image_file"];
};

const resetForm = () => {
    clearLocalStorage();
    formData.reset();
    errors.value = {};
    formData.proposed_changes.image_file = null;
    changedPicture.value = false;
    showReset.value = false;
    pictureKey.value++; // Force rerender of PictureInput
};

function onImageChange(event) {
    formData.proposed_changes.image_file = event.target.files[0];
    changedPicture.value = true;
    validateField("proposed_changes.image_file");
}

const validateField = async (field) => {
    try {
        await resourceEditsFields.validateAt(field, formData.data());
        delete errors.value[field];
    } catch (e) {
        if (e instanceof ValidationError) {
            errors.value[field] = e.message;
        }
    }
};

const submit = async () => {
    formData.clearErrors();
    errors.value = {};

    try {
        await resourceEditsFields.validate(formData.data(), {
            abortEarly: false,
        });

        // Remove image if there is no changes made to it
        const submissionData = { ...formData.data() };
        if (!changedPicture.value) {
            delete submissionData.proposed_changes.image_file;
        }

        formData
            .transform(() => submissionData)
            .post(
                route("resource_edits.store", {
                    computerScienceResource: props.resource.id,
                }),
                {
                    onSuccess: () => clearLocalStorage(),
                    onError: (serverErrors) => {
                        formData.setError(serverErrors);
                    },
                }
            );
    } catch (e) {
        if (e instanceof ValidationError) {
            e.inner.forEach((error) => {
                errors.value[error.path] = error.message;
            });
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
                    class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg p-6 relative border border-primary/10 dark:border-primary/20"
                >
                    <div class="flex-row flex my-2">
                        <BackButton
                            :route="
                                route('resources.show', {
                                    slug: props.resource.slug,
                                    tab: 'edits',
                                })
                            "
                        />
                        <FormSaverChip
                            :is-saved="isSavedToLocalStorage"
                            :has-content="hasFormContent"
                        />
                    </div>
                    <form @submit.prevent="submit">
                        <div class="mb-9 rounded-lg">
                            <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">
                                Describe Your Change
                            </h2>
                            <div>
                                <InputLabel for="edit_title"
                                    >Title
                                    <span class="text-red-500"> * </span>
                                </InputLabel>
                                <TextInput
                                    id="edit_title"
                                    v-model="formData.edit_title"
                                    @blur="validateField('edit_title')"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError
                                    class="mt-2"
                                    :message="
                                        errors.edit_title ||
                                        formData.errors.edit_title
                                    "
                                />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="edit_description"
                                    >Description
                                    <span class="text-red-500"> * </span>
                                </InputLabel>
                                <TextArea
                                    id="edit_description"
                                    v-model="formData.edit_description"
                                    @blur="validateField('edit_description')"
                                    class="mt-1 block w-full"
                                    :rows="4"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="
                                        errors.edit_description ||
                                        formData.errors.edit_description
                                    "
                                />
                            </div>
                        </div>

                        <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">
                            New Edited Resource
                        </h2>
                        <div class="p-4 border rounded bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel
                                        for="name"
                                        value="Resource Name"
                                    />
                                    <TextInput
                                        id="name"
                                        v-model="formData.proposed_changes.name"
                                        @blur="
                                            validateField(
                                                'proposed_changes.name'
                                            )
                                        "
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errors['proposed_changes.name'] ||
                                            formData.errors[
                                                'proposed_changes.name'
                                            ]
                                        "
                                    />
                                </div>

                                <div>
                                    <InputLabel value="Image Thumbnail" />
                                    <div
                                        class="flex flex-col items-center gap-2"
                                    >
                                        <PictureInput
                                            :prefill="props.resource.image_url"
                                            :key="pictureKey"
                                            ref="pictureInput"
                                            width="220"
                                            height="220"
                                            margin="16"
                                            accept="image/jpeg,image/png"
                                            size="0.4"
                                            remove-button-class="inline-flex items-center px-4 py-2 bg-primary border-0 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 focus:bg-primary/90 active:bg-primary/80 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150"
                                            button-class="inline-flex items-center px-4 py-2 border border-primary rounded-md font-semibold text-xs text-primary uppercase tracking-widest hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition ease-in-out duration-150 mr-4"
                                            removable
                                            @remove="
                                                () => (changedPicture = true)
                                            "
                                            @change="onImageChange"
                                        />
                                        <SecondaryButton
                                            v-if="props.resource.image_url"
                                            @click="resetImage"
                                            type="button"
                                        >
                                            Reset To Original Image
                                        </SecondaryButton>
                                    </div>
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errors[
                                                'proposed_changes.image_file'
                                            ] ||
                                            formData.errors[
                                                'proposed_changes.image_file'
                                            ]
                                        "
                                    />
                                </div>

                                <div>
                                    <InputLabel for="page_url" value="URL" />
                                    <TextInput
                                        id="page_url"
                                        v-model="
                                            formData.proposed_changes.page_url
                                        "
                                        @blur="
                                            validateField(
                                                'proposed_changes.page_url'
                                            )
                                        "
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errors[
                                                'proposed_changes.page_url'
                                            ] ||
                                            formData.errors[
                                                'proposed_changes.page_url'
                                            ]
                                        "
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
                                    v-model="
                                        formData.proposed_changes.description
                                    "
                                    @blur="
                                        validateField(
                                            'proposed_changes.description'
                                        )
                                    "
                                    class="mt-1 block w-full"
                                    :rows="8"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="
                                        errors[
                                            'proposed_changes.description'
                                        ] ||
                                        formData.errors[
                                            'proposed_changes.description'
                                        ]
                                    "
                                />
                            </div>

                            <div
                                class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4"
                            >
                                <div>
                                    <InputLabel
                                        for="difficulties"
                                        value="Difficulty"
                                    />
                                    <MultiSelect
                                        id="difficulties"
                                        v-model="
                                            formData.proposed_changes.difficulties
                                        "
                                        @blur="
                                            validateField(
                                                'proposed_changes.difficulty'
                                            )
                                        "
                                        :options="difficultiesObject"
                                        option-label="label"
                                        option-value="value"
                                        placeholder="Select Difficulty"
                                        class="w-full mt-1"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errors[
                                                'proposed_changes.difficulties'
                                            ] ||
                                            formData.errors[
                                                'proposed_changes.difficulties'
                                            ]
                                        "
                                    />
                                </div>

                                <div>
                                    <InputLabel for="pricing" value="Pricing" />
                                    <Select
                                        id="pricing"
                                        v-model="
                                            formData.proposed_changes.pricing
                                        "
                                        @blur="
                                            validateField(
                                                'proposed_changes.pricing'
                                            )
                                        "
                                        :options="pricingsObject"
                                        option-label="label"
                                        option-value="value"
                                        placeholder="Select Pricing"
                                        class="w-full mt-1"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errors[
                                                'proposed_changes.pricing'
                                            ] ||
                                            formData.errors[
                                                'proposed_changes.pricing'
                                            ]
                                        "
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="platforms"
                                        value="Platforms"
                                    />
                                    <MultiSelect
                                        id="platforms"
                                        v-model="
                                            formData.proposed_changes.platforms
                                        "
                                        @blur="
                                            validateField(
                                                'proposed_changes.platforms'
                                            )
                                        "
                                        :options="platformsObject"
                                        option-label="label"
                                        option-value="value"
                                        placeholder="Select Platforms"
                                        class="w-full mt-1"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errors[
                                                'proposed_changes.platforms'
                                            ] ||
                                            formData.errors[
                                                'proposed_changes.platforms'
                                            ]
                                        "
                                    />
                                </div>
                            </div>

                            <div class="mt-4">
                                <InputLabel value="Topic Tags" />
                                <TagSelector
                                    :tag-type="'topics_tags'"
                                    v-model="
                                        formData.proposed_changes.topic_tags
                                    "
                                    @blur="
                                        validateField(
                                            'proposed_changes.topic_tags'
                                        )
                                    "
                                />
                                <InputError
                                    class="mt-2"
                                    :message="
                                        errors['proposed_changes.topic_tags'] ||
                                        formData.errors[
                                            'proposed_changes.topic_tags'
                                        ]
                                    "
                                />
                            </div>

                            <div class="mt-4">
                                <InputLabel value="Programming Language Tags" />
                                <TagSelector
                                    :tag-type="'programming_languages_tags'"
                                    v-model="
                                        formData.proposed_changes
                                            .programming_language_tags
                                    "
                                    @blur="
                                        validateField(
                                            'proposed_changes.programming_language_tags'
                                        )
                                    "
                                />
                                <InputError
                                    class="mt-2"
                                    :message="
                                        errors[
                                            'proposed_changes.programming_language_tags'
                                        ] ||
                                        formData.errors[
                                            'proposed_changes.programming_language_tags'
                                        ]
                                    "
                                />
                            </div>

                            <div class="mt-4">
                                <InputLabel value="General Tags" />
                                <TagSelector
                                    :tag-type="'general_tags'"
                                    v-model="
                                        formData.proposed_changes.general_tags
                                    "
                                    @blur="
                                        validateField(
                                            'proposed_changes.general_tags'
                                        )
                                    "
                                />
                                <InputError
                                    class="mt-2"
                                    :message="
                                        errors[
                                            'proposed_changes.general_tags'
                                        ] ||
                                        formData.errors[
                                            'proposed_changes.general_tags'
                                        ]
                                    "
                                />
                            </div>

                            <div
                                class="flex items-center justify-between mt-6 p-4"
                            >
                                <SecondaryButton
                                    @click="showReset = true"
                                    type="button"
                                >
                                    Reset
                                </SecondaryButton>
                                <ConfirmationModal
                                    :show="showReset"
                                    @close="showReset = false"
                                >
                                    <template #title> Reset the form </template>

                                    <template #content>
                                        Are you sure you want reset back to the
                                        default values for this resource? You
                                        will lose your saved changes.
                                    </template>

                                    <template #footer>
                                        <SecondaryButton
                                            @click="showReset = false"
                                        >
                                            Cancel
                                        </SecondaryButton>

                                        <DangerButton
                                            class="ms-3"
                                            :class="{
                                                'opacity-25':
                                                    formData.processing,
                                            }"
                                            :disabled="formData.processing"
                                            @click="resetForm"
                                        >
                                            Reset Form
                                        </DangerButton>
                                    </template>
                                </ConfirmationModal>

                                <PrimaryButton
                                    :class="{
                                        'opacity-25': formData.processing,
                                    }"
                                    :disabled="formData.processing"
                                    type="submit"
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
