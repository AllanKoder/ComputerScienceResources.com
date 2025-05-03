<script setup>
import { ref, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { platforms, pricings, difficulties } from '@/Helpers/labels'
import InputText from 'primevue/inputtext'
import MultiSelect from 'primevue/multiselect'
import Button from 'primevue/button'

const name = ref('')
const description = ref('')
const selectedPlatforms = ref([])
const selectedDifficulty = ref([])
const selectedPricing = ref([])

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)

  // single‐value fields
  name.value = urlParams.get('name') || ''
  description.value = urlParams.get('description') || ''

  // multi‐value fields that may be named like difficulty[0], difficulty[1], …
  selectedPlatforms.value = extractIndexedArray(urlParams, 'platforms')
  selectedDifficulty.value = extractIndexedArray(urlParams, 'difficulty')
  selectedPricing.value  = extractIndexedArray(urlParams, 'pricing')
})

/**
 * Pulls out all params named `${base}[0]`, `${base}[1]`, … into a flat array.
 */
function extractIndexedArray(urlParams, base) {
  const result = []
  for (const [key, value] of urlParams) {
    if (key === base || key.startsWith(base + '[')) {
      result.push(value)
    }
  }
  return result
}

function search() {
  router.visit(
    route('resources.index', {
      name: name.value || undefined,
      description: description.value || undefined,
      platforms: selectedPlatforms.value.length ? selectedPlatforms.value : undefined,
      difficulty: selectedDifficulty.value.length ? selectedDifficulty.value : undefined,
      pricing:  selectedPricing.value.length  ? selectedPricing.value  : undefined,
    }),
    { preserveScroll: true }
  )
}
</script>

<template>
  <form @submit.prevent="search" class="p-6 bg-white rounded-xl shadow-md mb-4">
    <div class="flex flex-wrap gap-4">
      <!-- Name -->
      <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium mb-1">Name</label>
        <InputText v-model="name" class="w-full" />
      </div>

      <!-- Description -->
      <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium mb-1">Description</label>
        <InputText v-model="description" class="w-full" />
      </div>

      <!-- Platforms -->
      <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium mb-1">Platforms</label>
        <MultiSelect
          v-model="selectedPlatforms"
          :options="platforms"
          optionLabel="label"
          optionValue="value"
          placeholder="All Platforms"
          showClear
          class="w-full"
        />
      </div>

      <!-- Difficulty -->
      <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium mb-1">Difficulty</label>
        <MultiSelect
          v-model="selectedDifficulty"
          :options="difficulties"
          optionLabel="label"
          optionValue="value"
          placeholder="All Difficulties"
          showClear
          class="w-full"
        />
      </div>

      <!-- Pricing -->
      <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium mb-1">Pricing</label>
        <MultiSelect
          v-model="selectedPricing"
          :options="pricings"
          optionLabel="label"
          optionValue="value"
          placeholder="All Pricing"
          showClear
          class="w-full"
        />
      </div>

      <!-- Search Button -->
      <div class="flex items-end">
        <Button label="Filter" icon="pi pi-search" type="submit" />
      </div>
    </div>
  </form>
</template>
