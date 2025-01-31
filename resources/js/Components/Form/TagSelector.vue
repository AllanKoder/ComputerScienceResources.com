<script setup>
import { Tag } from "primevue";
import { Icon } from "@iconify/vue";
import { ref } from "vue";
import AutoComplete from "primevue/autocomplete";
import { defineEmits, defineProps } from "vue";

const props = defineProps({
    queryUrl: String
});

const emit = defineEmits(['changed']);

const tags = ref(new Set());
const searchValue = ref("");
const allSuggestions = ref(["test1", "test2"]);
const suggestions = ref([]);
const emptySearchMessage = ref("");

const addTag = (tag) => {
    if (tag && !tags.value.has(tag)) {
        tags.value.add(tag);
        searchValue.value = "";

        emit('changed', tags)
    }
};

const removeTag = (tag) => {
    if (tag) {
        tags.value.delete(tag);

        emit('changed', tags)
    }
};

const handleSelect = (event) => {
    addTag(event.value);
};

const handleKeydown = (event) => {
    if (event.key === "Enter" && searchValue.value.trim()) {
        addTag(searchValue.value.trim());
        event.preventDefault();
    }
};

const filterSuggestions = (event) => {
    let query = event.query.toLowerCase();
    suggestions.value = allSuggestions.value.filter((item) =>
        item.toLowerCase().includes(query)
    );

    if (suggestions.value.length == 0) {
        emptySearchMessage.value = searchValue.value;
    }
};
</script>

<template>
    <div class="flex col-auto gap-3 flex-col justify-center items-center">
        <!-- Search bar to add tags -->
        <AutoComplete
            v-model="searchValue"
            :suggestions="suggestions"
            :empty-search-message="emptySearchMessage"
            @complete="filterSuggestions"
            @item-select="handleSelect"
            @keydown="handleKeydown"
            placeholder="Type to add tags"
        >
            <template #option="headerProps">
                {{ headerProps.option }} - 
                <span class="py-0.5 px-1 rounded-lg bg-gray-100">32</span> 
            </template>
        </AutoComplete>

        <!-- List of tags -->
        <div class="mt-2">
            <Tag v-for="tag in tags" :key="tag" class="mr-2 mb-2">
                <button @click="() => removeTag(tag)" class="mr-1">
                    <Icon icon="mdi:remove-bold" />
                </button>
                <span class="text-base">{{ tag }}</span>
            </Tag>
        </div>
    </div>
</template>
