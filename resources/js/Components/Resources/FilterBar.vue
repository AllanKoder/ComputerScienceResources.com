<script setup>
import { ref, onMounted } from "vue";
import { Icon } from "@iconify/vue";
import { router } from "@inertiajs/vue3";
import { platforms, pricings, difficulties } from "@/Helpers/labels";
import InputText from "primevue/inputtext";
import MultiSelect from "primevue/multiselect";
import Button from "primevue/button";
import Rating from "primevue/rating";
import Calendar from 'primevue/calendar';

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
const selectedOverallRating = ref(null);
const selectedCommunityRating = ref(null);
const selectedTeachingClarity = ref(null);
const selectedEngagement = ref(null);
const selectedPracticality = ref(null);
const selectedUserFriendliness = ref(null);
const selectedUpdates = ref(null);

// date filters
const createdFrom = ref(null);
const createdTo = ref(null);
const updatedFrom = ref(null);
const updatedTo = ref(null);

const advancedOpen = ref(false);

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
        overall_rating: selectedOverallRating,
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

    createdFrom.value = urlParams.get("created_from")
        ? new Date(urlParams.get("created_from"))
        : null;
    createdTo.value = urlParams.get("created_to")
        ? new Date(urlParams.get("created_to"))
        : null;
    updatedFrom.value = urlParams.get("updated_from")
        ? new Date(urlParams.get("updated_from"))
        : null;
    updatedTo.value = urlParams.get("updated_to")
        ? new Date(urlParams.get("updated_to"))
        : null;
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
            overall_rating: selectedOverallRating.value || undefined,
            community_rating: selectedCommunityRating.value || undefined,
            teaching_clarity: selectedTeachingClarity.value || undefined,
            engagement: selectedEngagement.value || undefined,
            practicality: selectedPracticality.value || undefined,
            user_friendliness: selectedUserFriendliness.value || undefined,
            updates: selectedUpdates.value || undefined,

            created_from:
                createdFrom.value?.toISOString().slice(0, 10) || undefined,
            created_to:
                createdTo.value?.toISOString().slice(0, 10) || undefined,
            updated_from:
                updatedFrom.value?.toISOString().slice(0, 10) || undefined,
            updated_to:
                updatedTo.value?.toISOString().slice(0, 10) || undefined,
        }),
        { preserveScroll: true }
    );
}

function resetFilters() {
    // Text
    name.value = "";
    description.value = "";

    // Arrays
    selectedPlatforms.value = [];
    selectedDifficulty.value = [];
    selectedPricing.value = [];
    selectedTopics.value = [];
    selectedProgrammingLanguages.value = [];
    selectedGeneralTags.value = [];

    // Ratings
    selectedOverallRating.value = null;
    selectedCommunityRating.value = null;
    selectedTeachingClarity.value = null;
    selectedEngagement.value = null;
    selectedPracticality.value = null;
    selectedUserFriendliness.value = null;
    selectedUpdates.value = null;

    // Dates
    createdFrom.value = null;
    createdTo.value = null;
    updatedFrom.value = null;
    updatedTo.value = null;
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
                <InputText v-model="name" placeholder="Title" class="w-full" />
            </div>

            <!-- Description -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1"
                    >Description</label
                >
                <InputText
                    v-model="description"
                    placeholder="Enter Keywords"
                    class="w-full"
                />
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
                    >Min. Overall Rating</label
                >
                <Rating
                    v-model="selectedOverallRating"
                    :stars="4"
                    cancel
                    class="text-yellow-400"
                />
            </div>

            <!-- Advanced Filters Section -->
            <div
                v-if="advancedOpen"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 w-full mt-2"
            >
                <!-- Created Date Range -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium mb-1"
                        >Created From</label
                    >
                    <Calendar
                        v-model="createdFrom"
                        showIcon
                        dateFormat="yy-mm-dd"
                        class="w-full"
                    />
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium mb-1"
                        >Created To</label
                    >
                    <Calendar
                        v-model="createdTo"
                        showIcon
                        dateFormat="yy-mm-dd"
                        class="w-full"
                    />
                </div>

                <!-- Updated Date Range -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium mb-1"
                        >Updated From</label
                    >
                    <Calendar
                        v-model="updatedFrom"
                        showIcon
                        dateFormat="yy-mm-dd"
                        class="w-full"
                    />
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium mb-1"
                        >Updated To</label
                    >
                    <Calendar
                        v-model="updatedTo"
                        showIcon
                        dateFormat="yy-mm-dd"
                        class="w-full"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1"
                        >Min. Community</label
                    >
                    <Rating
                        v-model="selectedCommunityRating"
                        :stars="4"
                        cancel
                        class="text-yellow-400"
                    />
                </div>
                <div>
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
                <div>
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
                <div>
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
                <div>
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
                <div>
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
            </div>
            <div class="flex items-end flex-wrap gap-4 w-full">
                <!-- Advanced Filters Toggle (now on the left) -->
                <div>
                    <button
                        type="button"
                        class="flex items-center gap-1 text-sm text-blue-600 underline focus:outline-none"
                        @click="advancedOpen = !advancedOpen"
                    >
                        <Icon
                            :icon="
                                advancedOpen
                                    ? 'mdi:chevron-up'
                                    : 'mdi:chevron-down'
                            "
                            class="w-4 h-4 transition-transform duration-200"
                        />
                        {{
                            advancedOpen
                                ? "Hide Advanced Filters"
                                : "Show Advanced Filters"
                        }}
                    </button>
                </div>

                <!-- Reset & Filter Buttons (now on the right) -->
                <div class="flex gap-4 ml-auto">
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
        </div>
    </form>
</template>
