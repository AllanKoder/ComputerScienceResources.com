<script setup>
import { ref, watch } from "vue";
import TagSelector from "@/Components/Form/TagSelector.vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    field: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(["change"]);

const localForm = ref({ ...props.form });

const changeTags = (newTags, name) => {
    localForm.value = {
        ...localForm.value,
        [name]: newTags.value,
    };
    emit("change", localForm.value);
};

// Watch for changes in the form prop
watch(
    () => props.form,
    (newForm) => {
        localForm.value = { ...newForm };
    },
    { deep: true }
);
</script>

<template>
    <div class="space-y-4">
        <TagSelector>
            @changed="(newTags) => changeTags(newTags, field);"
        </TagSelector>
    </div>
</template>
