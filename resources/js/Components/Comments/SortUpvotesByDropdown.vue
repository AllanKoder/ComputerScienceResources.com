<script setup>
import { ref, defineEmits } from "vue";
import { Select } from "primevue";

const props = defineProps({
    initialValue: {
        type: String,
        default: 'top',
    }
})

const selectedSort = ref(props.initialValue);

const sortOptions = [
    { label: "Top", value: "top" },
    { label: "Bottom", value: "bottom" },
    { label: "Hot", value: "hot" },
    { label: "Controversial", value: "controversial" },
    { label: "Total Votes", value: "total_votes" },
    { label: "Latest", value: "latest" },
    { label: "Oldest", value: "oldest" },
    { label: "Recently Updated", value: "recently_updated" },
];

const emit = defineEmits(['change'])
</script>

<template>
    <div class="flex items-center gap-2 py-4">
        <label class="font-semibold text-gray-700">Sort by:</label>
        <Select
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
