<script setup>
import { ref, watch } from 'vue';
import InputText from 'primevue/inputtext';
import { Icon } from "@iconify/vue";

const emit = defineEmits(['update']);

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

// Initialize the items array from initialValues (do not auto-add an empty field)
const items = ref([...props.initialValues]);

// Watch for changes in items and emit the JSON string of non-empty values.
watch(
  items,
  (newItems) => {
    const jsonOutput = JSON.stringify(newItems.filter((item) => item !== ""));
    emit("update", jsonOutput);
  },
  { deep: true, immediate: true }
);

// Add a new (empty) item if maxSize hasn't been reached.
const addItem = () => {
  if (items.value.length < props.maxSize) {
    items.value.push("");
  }
};

// Optional: Remove an item from the list.
const removeItem = (index) => {
  items.value.splice(index, 1);
};
</script>

<template>
  <div class="flex flex-col gap-2">
    <!-- Render each list item input -->
    <div v-for="(item, index) in items" :key="index" class="flex items-center gap-2">
      <InputText
        v-model="items[index]"
        placeholder="Enter an item"
        class="flex-grow"
      />
      <!-- Show a remove button if more than one item exists -->
      <button
        v-if="items.length > 1"
        type="button"
        @click="removeItem(index)"
        class="text-red-500 focus:outline-none"
      >
        <Icon icon="mdi:close" />
      </button>
    </div>
    <!-- Add button -->
    <button
      type="button"
      @click="addItem"
      class="flex items-center gap-1 text-blue-500 focus:outline-none"
    >
      <Icon icon="mdi:plus" />
      <span>Add</span>
    </button>
  </div>
</template>
