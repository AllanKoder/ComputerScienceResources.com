<script setup>
import { defineProps, ref, watch, nextTick } from "vue";
import { Tag } from "primevue";
import { Icon } from "@iconify/vue";
import AutoComplete from "primevue/autocomplete";
import { defineModel } from "vue";
import axios from "axios";

const props = defineProps({
    tagType: {
        type: String,
        required: true,
    }
})

const model = defineModel(); // v-model from parent

const selectedTags = ref([]);
const searchValue = ref("");
const tagResult = ref([]);
const tagCount = ref({});
const emptySearchMessage = ref("");

// Sync initial model value to internal state
watch(
    () => model.value,
    (newVal) => {
        if (Array.isArray(newVal)) {
            selectedTags.value = [...newVal];
        }
    },
    { immediate: true }
);

function sanitizeTag(tag) {
    // Remove trailing spaces and lowercase
    let transformedTag = tag.trim().toLowerCase();

    // Apply rules from config('computerScienceResources.tags_rules')
    // Transform the tag to lowercase and replace spaces with hyphens
    transformedTag = transformedTag.replace(/\s+/g, "-");
    // Allow only characters matching regex: /^[a-z0-9+#.-]+$/
    // i.e., keep lowercase letters, digits, plus, hash, dot, and hyphen
    transformedTag = transformedTag.replace(/[^a-z0-9+#.-]/g, "");
    return transformedTag;
}

const addTag = (tag) => {
    if (tag) {
        let transformedTag = sanitizeTag(tag);

        if (!selectedTags.value.includes(transformedTag)) {
            selectedTags.value.push(transformedTag);
            model.value = [...selectedTags.value];
        }
    }
};

const removeTag = (tag) => {
    if (tag) {
        selectedTags.value = selectedTags.value.filter((t) => t !== tag);
        model.value = [...selectedTags.value];
    }
};

const handleSelect = (event) => {
    addTag(event.value);
    // Clear search value in next tick after Vue updates
    nextTick(() => {
        searchValue.value = "";
    });
};

const handleKeydown = (event) => {
    if (event.key === "Enter") {
        if (searchValue.value) {
            addTag(searchValue.value);
            // Clear the input after adding via Enter
            nextTick(() => {
                searchValue.value = "";
            });
        }
        event.preventDefault();
    }
};

const filterSuggestions = () => {
    let query = searchValue.value.trim().toLowerCase();

    axios
        .get(route("tags.search", { type: props.tagType, query }))
        .then((response) => {
            const tags = response.data.tags;

            tagResult.value = tags.map((tagJson) => tagJson.tag);

            tagCount.value = Object.fromEntries(
                tags.map((tagJson) => [tagJson.tag, tagJson.count])
            );
        })
        .catch(() => {
            console.warn("Cannot query server for tags");
            tagResult.value = [];
            tagCount.value = {};
        });
};
</script>

<template>
    <div class="mb-2">
        <!-- List of tags -->
        <div class="mb-2 flex flex-wrap">
            <Tag v-for="tag in selectedTags"
                 :key="tag"
                 class="mr-2 mb-2 bg-secondary border border-primary/10 text-primary px-2 py-1 rounded-full"
            >
                <button @click="() => removeTag(tag)"
                        class="mr-1 hover:text-primaryDark transition-colors focus:outline-none">
                    <Icon icon="mdi:remove-bold" class="w-4 h-4" />
                </button>
                <span class="text-sm">{{ tag }}</span>
            </Tag>
        </div>

        <!-- Search bar to add tags -->
        <AutoComplete
            v-model="searchValue"
            :suggestions="tagResult"
            :empty-search-message="emptySearchMessage"
            @complete="filterSuggestions"
            @item-select="handleSelect"
            @keydown="handleKeydown"
            placeholder="Type to add tags"
            completeOnFocus
            class="w-full"
            :class="{
                'min-h-[40px]': true,
                'shadow-sm': true,
            }"
            :inputClass="'w-full h-10 text-sm'"
            :panelClass="'bg-white border border-primary/10 shadow-lg rounded-lg'"
        >
            <template #option="slotProps">
                <div class="flex items-center justify-between w-full">
                    <span class="text-sm">{{ slotProps.option }}</span>
                    <span
                        v-if="tagCount[slotProps.option] !== undefined"
                        class="rounded-lg bg-secondary px-1 text-sm text-primaryDark"
                    >
                        {{ tagCount[slotProps.option] }}
                    </span>
                </div>
            </template>
        </AutoComplete>
    </div>
</template>
