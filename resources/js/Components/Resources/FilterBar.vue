<script setup>
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { platforms, pricings, difficulties } from '@/Helpers/labels'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import MultiSelect from 'primevue/multiselect'
import Button from 'primevue/button'

// Get current query params
const { url } = usePage()
const query = new URLSearchParams(url.split('?')[1])

// Reactive form fields
const name = ref(query.get('name') || '')
const description = ref(query.get('description') || '')
const selectedPlatforms = ref(query.getAll('platforms') || [])
const selectedDifficulty = ref(query.get('difficulty') || '')
const selectedPricing = ref(query.get('pricing') || '')

function search() {
  router.visit(route('resources.index', {
    name: name.value || undefined,
    description: description.value || undefined,
    platforms: selectedPlatforms.value?.length ? selectedPlatforms.value : undefined,
    difficulty: selectedDifficulty.value || undefined,
    pricing: selectedPricing.value || undefined,
  }), {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>

<template>
  <div class="p-4 bg-white rounded-xl shadow-md">
    <h2 class="text-xl font-semibold mb-4">Search Resources</h2>

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
        <Dropdown
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
        <Dropdown
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
        <Button label="Filter" icon="pi pi-search" @click="search" />
      </div>
    </div>
  </div>
</template>
