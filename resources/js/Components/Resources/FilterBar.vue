<script setup>
import { ref, onMounted } from "vue";
import { Icon } from "@iconify/vue";
import { router } from "@inertiajs/vue3";
import {
    platformsObject,
    pricingsObject,
    difficultiesObject,
    resourceSortingLabels,
} from "@/Helpers/labels";
import TagSelector from "@/Components/Form/TagSelector.vue";
import InputText from "primevue/inputtext";
import MultiSelect from "primevue/multiselect";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Rating from "primevue/rating";
import Calendar from "primevue/calendar";

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

// sort_by options
const selectedSorting = ref("top");
const selectedReverse = ref(false);

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
        overall: selectedOverallRating,
        community: selectedCommunityRating,
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

    // initialize sort_by
    selectedSorting.value = urlParams.get("sort_by") || "top";
    selectedReverse.value = urlParams.get("reverse") === "true";

    createdFrom.value = urlParams.get("created_from")
        ? new Date(urlParams.get("created_from") + "T00:00:00")
        : null;
    createdTo.value = urlParams.get("created_to")
        ? new Date(urlParams.get("created_to") + "T00:00:00")
        : null;
    updatedFrom.value = urlParams.get("updated_from")
        ? new Date(urlParams.get("updated_from") + "T00:00:00")
        : null;
    updatedTo.value = urlParams.get("updated_to")
        ? new Date(urlParams.get("updated_to") + "T00:00:00")
        : null;

    function isAnyAdvancedFilterSet() {
        return (
            selectedCommunityRating.value !== null ||
            selectedTeachingClarity.value !== null ||
            selectedEngagement.value !== null ||
            selectedPracticality.value !== null ||
            selectedUserFriendliness.value !== null ||
            selectedUpdates.value !== null ||
            createdFrom.value !== null ||
            createdTo.value !== null ||
            updatedFrom.value !== null ||
            updatedTo.value !== null ||
            selectedSorting.value !== "top" ||
            selectedReverse.value == true
        );
    }

    advancedOpen.value = isAnyAdvancedFilterSet();
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

function selectSorting(option) {
    selectedSorting.value = option;
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
            overall: selectedOverallRating.value || undefined,
            community: selectedCommunityRating.value || undefined,
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
            sort_by: selectedSorting.value || undefined,
            reverse: selectedReverse.value == true ? 'true' : undefined,
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

    // Sorting
    selectedSorting.value = "top";
    selectedReverse.value = false;
}
</script>

<template>
    <form
        @submit.prevent="search"
        class="bg-white rounded-xl shadow-sm border border-primary/10 mb-4 max-w-8xl mx-auto"
    >
        <!-- Primary Search Section -->
        <div class="px-6 pt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name Search -->
                <div class="w-full">
                    <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                        <Icon icon="mdi:magnify" class="w-4 h-4" />
                        Resource Name
                    </label>
                    <InputText
                        v-model="name"
                        placeholder="Search by title..."
                        class="w-full h-10 text-sm shadow-sm"
                    />
                </div>

                <!-- Description Search -->
                <div class="w-full">
                    <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                        <Icon icon="mdi:text" class="w-4 h-4" />
                        Description
                    </label>
                    <InputText
                        v-model="description"
                        placeholder="Search in content..."
                        class="w-full h-10 text-sm shadow-sm"
                    />
                </div>
            </div>
        </div>

        <!-- Quick Filters Section -->
        <div class="px-6 py-6 border-b border-primary/10 bg-secondary/5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-8 gap-4">
                <!-- Platform Filter -->
                <div class="w-full lg:col-span-2">
                    <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                        <Icon icon="mdi:devices" class="w-4 h-4" />
                        Platform
                    </label>
                    <MultiSelect
                        v-model="selectedPlatforms"
                        :options="platformsObject"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="All Platforms"
                        class="w-full min-h-[40px]"
                    />
                </div>

                <!-- Difficulty Level -->
                <div class="w-full lg:col-span-1">
                    <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                        <Icon icon="mdi:stairs" class="w-4 h-4" />
                        Difficulty
                    </label>
                    <MultiSelect
                        v-model="selectedDifficulty"
                        :options="difficultiesObject"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="All Levels"
                        class="w-full min-h-[40px]"
                    />
                </div>

                <!-- Pricing -->
                <div class="w-full lg:col-span-1">
                    <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                        <Icon icon="mdi:currency-usd" class="w-4 h-4" />
                        Pricing
                    </label>
                    <MultiSelect
                        v-model="selectedPricing"
                        :options="pricingsObject"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Any Price"
                        class="w-full min-h-[40px]"
                    />
                </div>

                <!-- Overall Rating -->
                <div class="w-full lg:col-span-1">
                    <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                        <Icon icon="mdi:star" class="w-4 h-4" />
                        Min Rating
                    </label>
                    <div class="flex items-center h-10">
                        <Rating
                            v-model="selectedOverallRating"
                            :stars="4"
                            cancel
                            class="text-accent"
                        />
                    </div>
                </div>

                <!-- Topics Filter -->
                <div class="w-full lg:col-span-1">
                    <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                        <Icon icon="mdi:tag-multiple" class="w-4 h-4" />
                        Topics
                    </label>
                    <TagSelector
                        v-model="selectedTopics"
                        class="w-full"
                    />
                </div>

                <!-- Programming Languages Filter -->
                <div class="w-full lg:col-span-1">
                    <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                        <Icon icon="mdi:language-javascript" class="w-4 h-4" />
                        Languages
                    </label>
                    <TagSelector
                        v-model="selectedProgrammingLanguages"
                        class="w-full"
                    />
                </div>

                <!-- General Tags Filter -->
                <div class="w-full lg:col-span-1">
                    <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                        <Icon icon="mdi:tag" class="w-4 h-4" />
                        Tags
                    </label>
                    <TagSelector
                        v-model="selectedGeneralTags"
                        class="w-full"
                    />
                </div>
            </div>
        </div>

        <!-- Advanced Filters Section -->
        <div v-if="advancedOpen" class="p-6 bg-secondary/5 space-y-6">
            <!-- Ratings Section -->
            <div class="space-y-3">
                <h3 class="flex items-center gap-2 text-sm font-medium text-gray-600">
                    <Icon icon="mdi:star-settings" class="w-4 h-4" />
                    Rating Filters
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <!-- Community Rating -->
                    <div class="flex flex-col items-center">
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                            <Icon icon="mdi:account-group" class="w-4 h-4" />
                            Community
                        </label>
                        <Rating
                            v-model="selectedCommunityRating"
                            :stars="4"
                            cancel
                            class="text-accent"
                        />
                    </div>

                    <!-- Teaching Rating -->
                    <div class="flex flex-col items-center">
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                            <Icon icon="mdi:school" class="w-4 h-4" />
                            Teaching
                        </label>
                        <Rating
                            v-model="selectedTeachingClarity"
                            :stars="4"
                            cancel
                            class="text-accent"
                        />
                    </div>

                    <!-- Engagement Rating -->
                    <div class="flex flex-col items-center">
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                            <Icon icon="mdi:thumb-up" class="w-4 h-4" />
                            Engagement
                        </label>
                        <Rating
                            v-model="selectedEngagement"
                            :stars="4"
                            cancel
                            class="text-accent"
                        />
                    </div>

                    <!-- Practicality Rating -->
                    <div class="flex flex-col items-center">
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                            <Icon icon="mdi:tools" class="w-4 h-4" />
                            Practicality
                        </label>
                        <Rating
                            v-model="selectedPracticality"
                            :stars="4"
                            cancel
                            class="text-accent"
                        />
                    </div>

                    <!-- User Friendly Rating -->
                    <div class="flex flex-col items-center">
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                            <Icon icon="mdi:account-heart" class="w-4 h-4" />
                            User Friendly
                        </label>
                        <Rating
                            v-model="selectedUserFriendliness"
                            :stars="4"
                            cancel
                            class="text-accent"
                        />
                    </div>

                    <!-- Updates Rating -->
                    <div class="flex flex-col items-center">
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-2">
                            <Icon icon="mdi:update" class="w-4 h-4" />
                            Updates
                        </label>
                        <Rating
                            v-model="selectedUpdates"
                            :stars="4"
                            cancel
                            class="text-accent"
                        />
                    </div>
                </div>
            </div>

            <!-- Date Filters -->
            <div class="space-y-3">
                <h3 class="flex items-center gap-2 text-sm font-medium text-gray-600">
                    <Icon icon="mdi:calendar" class="w-4 h-4" />
                    Date Filters
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-600">
                            <Icon icon="mdi:calendar-plus" class="w-4 h-4" />
                            Created Date Range
                        </label>
                        <div class="flex gap-4">
                            <Calendar
                                v-model="createdFrom"
                                :max-date="createdTo"
                                showIcon
                                dateFormat="yy-mm-dd"
                                placeholder="From date"
                                class="flex-1"
                            />
                            <Calendar
                                v-model="createdTo"
                                :min-date="createdFrom"
                                showIcon
                                dateFormat="yy-mm-dd"
                                placeholder="To date"
                                class="flex-1"
                            />
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-600">
                            <Icon icon="mdi:calendar-refresh" class="w-4 h-4" />
                            Updated Date Range
                        </label>
                        <div class="flex gap-4">
                            <Calendar
                                v-model="updatedFrom"
                                :max-date="updatedTo"
                                showIcon
                                dateFormat="yy-mm-dd"
                                placeholder="From date"
                                class="flex-1"
                            />
                            <Calendar
                                v-model="updatedTo"
                                :min-date="updatedFrom"
                                showIcon
                                dateFormat="yy-mm-dd"
                                placeholder="To date"
                                class="flex-1"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sorting Section -->
            <div class="space-y-4">
                <h3 class="flex items-center gap-2 text-sm font-medium text-gray-600">
                    <Icon icon="mdi:sort" class="w-4 h-4" />
                    Sorting Options
                </h3>
                <div class="space-y-4">
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="opt in resourceSortingLabels"
                            :key="opt.value"
                            type="button"
                            @click="selectSorting(opt.value)"
                            :class="[
                                'px-4 py-2 rounded-full text-sm font-medium focus:outline-none transition-colors duration-200',
                                selectedSorting === opt.value
                                    ? 'bg-primary text-white shadow-sm'
                                    : 'bg-secondary text-primary hover:bg-secondaryDark',
                            ]"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                    <div>
                        <button
                            type="button"
                            @click="selectedReverse = !selectedReverse"
                            :class="[
                                'px-4 py-2 rounded-full text-sm font-medium focus:outline-none transition-colors duration-200',
                                selectedReverse
                                    ? 'bg-primary text-white shadow-sm'
                                    : 'bg-secondary text-primary hover:bg-secondaryDark',
                            ]"
                        >
                            Reverse Order
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="px-4 py-3 bg-white rounded-b-xl flex items-center justify-between border-t border-primary/10">
            <button
                type="button"
                class="flex items-center gap-2 text-sm text-primary hover:text-primaryDark focus:outline-none"
                @click="advancedOpen = !advancedOpen"
            >
                <Icon
                    :icon="advancedOpen ? 'mdi:chevron-up' : 'mdi:chevron-down'"
                    class="w-4 h-4 transition-transform duration-200"
                />
                <span class="font-medium">{{ advancedOpen ? 'Hide Advanced Filters' : 'Show Advanced Filters' }}</span>
            </button>
            <div class="flex gap-4">
                <SecondaryButton type="button" @click="resetFilters">
                    <Icon icon="mdi:refresh" class="mr-2" />
                    Reset
                </SecondaryButton>

                <PrimaryButton type="submit">
                    <Icon icon="mdi:search" class="mr-2" />
                    Apply Filters
                </PrimaryButton>
            </div>
        </div>
    </form>
</template>
