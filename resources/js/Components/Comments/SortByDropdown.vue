<script setup>
import { ref, defineEmits } from "vue";
import Dropdown from 'primevue/dropdown';

const props = defineProps({
    initialValue: {
        type: String,
        default: 'top',
    }
})

const selectedSort = ref(props.initialValue);

const sortOptions = [
    { label: "Latest", value: "latest" },
    { label: "Top", value: "top" },
    { label: "Bottom", value: "bottom" },
    { label: "Controversial", value: "controversial" },
    { label: "Posted by me", value: "mine" },
];

const emit = defineEmits(['change'])
</script>

<template>
    <div class="flex items-center gap-2 py-4">
        <label class="font-semibold text-gray-700">Sort by:</label>
        <Dropdown
            v-model="selectedSort"
            :options="sortOptions"
            optionLabel="label"
            optionValue="value"
            placeholder="Select"
            class="w-48"
            @change="() => emit('change', selectedSort)"
        />
    </div>
</template>
