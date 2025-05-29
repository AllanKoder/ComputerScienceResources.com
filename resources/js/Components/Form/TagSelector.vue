<script setup>
import { ref, watch } from "vue";
import { Tag } from "primevue";
import { Icon } from "@iconify/vue";
import AutoComplete from "primevue/autocomplete";
import { defineModel } from "vue";
import axios from "axios";

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

const addTag = (tag) => {
    if (tag && !selectedTags.value.includes(tag)) {
        selectedTags.value.push(tag);
        searchValue.value = "";
        model.value = [...selectedTags.value];
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
};

const handleKeydown = (event) => {
    if (event.key === "Enter" && searchValue.value.trim()) {
        addTag(searchValue.value.trim().toLowerCase());
        event.preventDefault();
    }
};

const filterSuggestions = () => {
    let query = searchValue.value.trim().toLowerCase();

    axios
        .get(route("tags.search", { query }))
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
    >
        <template #option="slotProps">
            <div class="flex items-center justify-between w-full">
                <span>{{ slotProps.option }}</span>
                <span
                    v-if="tagCount[slotProps.option] !== undefined"
                    class="py-0.5 px-1 rounded-lg bg-gray-100 text-sm text-gray-700"
                >
                    {{ tagCount[slotProps.option] }}
                </span>
            </div>
        </template>
    </AutoComplete>
    <!-- List of tags -->
    <div class="mt-2">
        <Tag v-for="tag in selectedTags" :key="tag" class="mr-2 mb-2">
            <button @click="() => removeTag(tag)" class="mr-1">
                <Icon icon="mdi:remove-bold" />
            </button>
            <span class="text-base">{{ tag }}</span>
        </Tag>
    </div>
</template>
