<script setup>
import { ref, watch } from "vue";
import InputText from "primevue/inputtext";
import { Icon } from "@iconify/vue";

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

watch(
    items,
    (newItems) => {
        emit(
            "change",
            newItems.filter((item) => item !== "")
        );
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
            <InputText
                :value="item"
                @input="(event) => updateItem(index, event.target.value)"
                placeholder="Enter an item"
                class="flex-grow"
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
            type="button"
            @click="addItem"
            class="flex items-center gap-1 text-blue-500 focus:outline-none"
            :disabled="items.length >= maxSize"
        >
            <Icon icon="mdi:plus" />
            <span>Add</span>
        </button>
    </div>
</template>
