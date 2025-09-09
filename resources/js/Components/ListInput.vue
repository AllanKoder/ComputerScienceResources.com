<script setup>
import { ref, watch } from "vue";
import { Icon } from "@iconify/vue";
import TextInput from "./TextInput.vue";

const emit = defineEmits(["change"]);

const props = defineProps({
    maxSize: {
        type: Number,
        default: 10,
    },
    initialValues: {
        type: Array,
        default: () => [],
    },
});

const items = ref([...props.initialValues]);

// Watch for changes to initialValues (e.g., when loading from localStorage)
watch(
    () => props.initialValues,
    (newInitialValues) => {
        // Only update if the content is actually different to avoid unnecessary updates
        const currentFiltered = items.value.filter((item) => item !== "");
        const newFiltered = newInitialValues.filter((item) => item !== "");

        if (JSON.stringify(currentFiltered) !== JSON.stringify(newFiltered)) {
            items.value = [...newInitialValues];
        }
    },
    { deep: true }
);

watch(
    items,
    (newItems) => {
        const filteredItems = newItems.filter((item) => item !== "");
        emit("change", filteredItems);
    },
    { deep: true, immediate: true }
);

const addItem = () => {
    if (items.value.length < props.maxSize) {
        items.value.push("");
    }
};

const removeItem = (index) => {
    items.value.splice(index, 1);
};

const updateItem = (index, value) => {
    items.value = [...items.value]; // Force reactivity
    items.value[index] = value;
};
</script>

<template>
    <div class="flex flex-col gap-2">
        <div
            v-for="(item, index) in items"
            :key="index"
            class="flex items-center gap-2"
        >
            <TextInput
                :value="item"
                @input="(event) => updateItem(index, event.target.value)"
                placeholder="Enter an item"
                class="flex-grow"
                :name="`list-item-${index}`"
            />
            <button
                type="button"
                @click="removeItem(index)"
                class="text-red-500 focus:outline-none"
            >
                <Icon icon="mdi:close" />
            </button>
        </div>

        <button
            v-if="items.length < maxSize"
            type="button"
            @click="addItem"
            class="flex items-center gap-1 text-blue-500 focus:outline-none"
        >
            <Icon icon="mdi:plus" />
            <span>Add</span>
        </button>
    </div>
</template>
