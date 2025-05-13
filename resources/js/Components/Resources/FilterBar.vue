<script setup>
import { ref, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { platforms, pricings, difficulties } from "@/Helpers/labels";
import InputText from "primevue/inputtext";
import MultiSelect from "primevue/multiselect";
import Button from "primevue/button";
import Rating from "primevue/rating";

import TagSelector from "@/Components/Form/TagSelector.vue";

// text filters
const name = ref("");
const description = ref("");

// multi-select filters
const selectedPlatforms = ref([]);
const selectedDifficulty = ref([]);
const selectedPricing = ref([]);
const selectedTopics = ref([]);
const selectedProgrammingLanguages = ref([]);
const selectedGeneralTags = ref([]);

// rating filters
const selectedCommunityRating = ref(null);
const selectedTeachingClarity = ref(null);
const selectedEngagement = ref(null);
const selectedPracticality = ref(null);
const selectedUserFriendliness = ref(null);
const selectedUpdates = ref(null);

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);

    name.value = urlParams.get("name") || "";
    description.value = urlParams.get("description") || "";

    selectedPlatforms.value = extractIndexedArray(urlParams, "platforms");
    selectedDifficulty.value = extractIndexedArray(urlParams, "difficulty");
    selectedPricing.value = extractIndexedArray(urlParams, "pricing");
    selectedTopics.value = extractIndexedArray(urlParams, "topics");
    selectedProgrammingLanguages.value = extractIndexedArray(
        urlParams,
        "programming_languages"
    );
    selectedGeneralTags.value = extractIndexedArray(urlParams, "general_tags");

    const ratingsMap = {
        community_rating: selectedCommunityRating,
        teaching_clarity: selectedTeachingClarity,
        engagement: selectedEngagement,
        practicality: selectedPracticality,
        user_friendliness: selectedUserFriendliness,
        updates: selectedUpdates,
    };
    for (const [param, refVar] of Object.entries(ratingsMap)) {
        const v = urlParams.get(param);
        refVar.value = v ? parseInt(v, 10) : null;
    }
});

function extractIndexedArray(urlParams, base) {
    const result = [];
    for (const [key, value] of urlParams) {
        if (key === base || key.startsWith(base + "[")) {
            result.push(value);
        }
    }
    return result;
}

function search() {
    router.visit(
        route("resources.index", {
            name: name.value || undefined,
            description: description.value || undefined,
            platforms: selectedPlatforms.value.length
                ? selectedPlatforms.value
                : undefined,
            difficulty: selectedDifficulty.value.length
                ? selectedDifficulty.value
                : undefined,
            pricing: selectedPricing.value.length
                ? selectedPricing.value
                : undefined,
            topics: selectedTopics.value.length
                ? selectedTopics.value
                : undefined,
            programming_languages: selectedProgrammingLanguages.value.length
                ? selectedProgrammingLanguages.value
                : undefined,
            general_tags: selectedGeneralTags.value.length
                ? selectedGeneralTags.value
                : undefined,
            community_rating: selectedCommunityRating.value || undefined,
            teaching_clarity: selectedTeachingClarity.value || undefined,
            engagement: selectedEngagement.value || undefined,
            practicality: selectedPracticality.value || undefined,
            user_friendliness: selectedUserFriendliness.value || undefined,
            updates: selectedUpdates.value || undefined,
        }),
        { preserveScroll: true }
    );
}

function resetFilters() {
    // text
    name.value = "";
    description.value = "";

    // arrays
    selectedPlatforms.value = [];
    selectedDifficulty.value = [];
    selectedPricing.value = [];
    selectedTopics.value = [];
    selectedProgrammingLanguages.value = [];
    selectedGeneralTags.value = [];

    // ratings
    selectedCommunityRating.value = null;
    selectedTeachingClarity.value = null;
    selectedEngagement.value = null;
    selectedPracticality.value = null;
    selectedUserFriendliness.value = null;
    selectedUpdates.value = null;
}
</script>

<template>
    <form
        @submit.prevent="search"
        class="p-6 bg-white rounded-xl shadow-md mb-4"
    >
        <div class="flex flex-wrap gap-4">
            <!-- Name -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1">Name</label>
                <InputText v-model="name" class="w-full" />
            </div>

            <!-- Description -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1"
                    >Description</label
                >
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

            <!-- Topics -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1">Topics</label>
                <TagSelector v-model="selectedTopics" />
            </div>

            <!-- Programming Languages -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1"
                    >Programming Languages</label
                >
                <TagSelector v-model="selectedProgrammingLanguages" />
            </div>

            <!-- Other Tags -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1">Other Tags</label>
                <TagSelector v-model="selectedGeneralTags" />
            </div>

            <!-- Star-rating filters -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1"
                    >Min. Community Rating</label
                >
                <Rating
                    v-model="selectedCommunityRating"
                    :stars="4"
                    cancel
                    class="text-yellow-400"
                />
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1"
                    >Min. Teaching Clarity</label
                >
                <Rating
                    v-model="selectedTeachingClarity"
                    :stars="4"
                    cancel
                    class="text-yellow-400"
                />
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1"
                    >Min. Engagement</label
                >
                <Rating
                    v-model="selectedEngagement"
                    :stars="4"
                    cancel
                    class="text-yellow-400"
                />
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1"
                    >Min. Practicality</label
                >
                <Rating
                    v-model="selectedPracticality"
                    :stars="4"
                    cancel
                    class="text-yellow-400"
                />
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1"
                    >Min. User Friendliness</label
                >
                <Rating
                    v-model="selectedUserFriendliness"
                    :stars="4"
                    cancel
                    class="text-yellow-400"
                />
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1"
                    >Min. Updates</label
                >
                <Rating
                    v-model="selectedUpdates"
                    :stars="4"
                    cancel
                    class="text-yellow-400"
                />
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-5">
                <Button
                    label="Reset"
                    icon="pi pi-refresh"
                    type="button"
                    class="p-button-secondary"
                    @click="resetFilters"
                />
                <Button label="Filter" icon="pi pi-search" type="submit" />
            </div>
        </div>
    </form>
</template>
