

<script setup>
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from "vue";
import { defineModel } from "vue";
import axios from "axios";
import { Icon } from "@iconify/vue";
import Tag from "@/Components/Tag.vue";

const DEBOUNCE_TIME = 450; // milliseconds

const props = defineProps({
    tagType: {
        type: String,
        required: true,
    },
    mode: {
        type: String,
        default: 'create', // 'create' or 'search'
        validator: (value) => ['create', 'search'].includes(value),
    },
    allowEverything: {
        type: Boolean,
        default: true,
    },
});

const model = defineModel();

// simple state
const selectedTags = ref([]);
const searchQuery = ref("");
const allTags = ref([]); // all tags from server (popular + search results)
const tagCounts = ref({});
const showDropdown = ref(false);
const highlightedIndex = ref(-1);
const searchInput = ref(null);
const isLoading = ref(false);

let searchTimeout = null;

// For dropdown teleport positioning
const dropdownStyles = ref({});
let inputEl = null;

function updateDropdownPosition() {
    if (!showDropdown.value) return;
    inputEl = searchInput.value;
    if (!inputEl) return;
    const rect = inputEl.getBoundingClientRect();
    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    dropdownStyles.value = {
        left: rect.left + scrollLeft + "px",
        top: rect.bottom + scrollTop + "px",
        width: rect.width + "px",
        position: "absolute",
    };
}
// Watch selectedTags and update dropdown position when tags change and dropdown is open
watch(selectedTags, () => {
    if (showDropdown.value) {
        nextTick(updateDropdownPosition);
    }
});

watch(showDropdown, (val) => {
    if (val) {
        nextTick(updateDropdownPosition);
    }
});

window.addEventListener("resize", updateDropdownPosition);
window.addEventListener("scroll", updateDropdownPosition, true);
onBeforeUnmount(() => {
    window.removeEventListener("resize", updateDropdownPosition);
    window.removeEventListener("scroll", updateDropdownPosition, true);
});

// computed properties
const availableTags = computed(() => {
    // Use a filtered/sanitized query for searching tags
    const filteredQuery = sanitizeTag(searchQuery.value);

    return allTags.value
        .filter((tagName) => {
            // don't show already selected tags
            if (selectedTags.value.includes(tagName)) return false;

            // if no query, show all
            if (!filteredQuery) return true;

            // filter by filtered query (sanitized)
            return tagName.toLowerCase().includes(filteredQuery);
        })
        .map((tagName) => ({
            name: tagName,
            count: tagCounts.value[tagName] || 0,
        }));
});

const canCreateNew = computed(() => {
    // In search mode, cannot create new tags
    if (props.mode === 'search') return false;

    const query = searchQuery.value.trim();
    if (!query || isLoading.value) return false;

    const sanitized = sanitizeTag(query);
    return (
        !selectedTags.value.includes(sanitized) &&
        !allTags.value.some(
            (tag) => tag.toLowerCase() === sanitized.toLowerCase()
        )
    );
});

// sync model
watch(
    () => model.value,
    (newVal) => {
        if (Array.isArray(newVal)) {
            selectedTags.value = [...newVal];
        }
    },
    { immediate: true }
);

function sanitizeTag(tag) {
    let transformedTag = tag.trim().toLowerCase();
    transformedTag = transformedTag.replace(/\s+/g, "-");
    transformedTag = transformedTag.replace(/[^a-z0-9+#.-]/g, "");
    return transformedTag;
}

function addTag(tag) {
    if (!tag) return;

    const transformedTag = sanitizeTag(tag);
    if (!selectedTags.value.includes(transformedTag)) {
        selectedTags.value.push(transformedTag);
        model.value = [...selectedTags.value];
    }
}

function removeTag(tag) {
    selectedTags.value = selectedTags.value.filter((t) => t !== tag);
    model.value = [...selectedTags.value];
}

function selectTag(tag) {
    addTag(tag);
    searchQuery.value = "";
    // Keep dropdown open after selecting a tag
    // showDropdown.value = false;
}

function addMultipleTags(input) {
    const tags = input.split(',').map(t => t.trim()).filter(t => t);
    tags.forEach(tag => addTag(tag));
    searchQuery.value = "";
}

function onInput() {
    showDropdown.value = true;
    highlightedIndex.value = -1;

    // clear previous timeout
    clearTimeout(searchTimeout);

    const query = searchQuery.value.trim();
    if (query) {
        isLoading.value = true;
        searchTimeout = setTimeout(() => searchTags(query), DEBOUNCE_TIME);
    }
}

function onKeydown(event) {
    if (event.key === "Enter") {
        event.preventDefault();

        if (
            highlightedIndex.value >= 0 &&
            highlightedIndex.value < availableTags.value.length
        ) {
            selectTag(availableTags.value[highlightedIndex.value].name);
        } else if (
            highlightedIndex.value === availableTags.value.length &&
            canCreateNew.value
        ) {
            selectTag(searchQuery.value.trim());
        } else if (props.mode === 'create' && searchQuery.value.trim()) {
            // Only allow adding tags in create mode
            // Handle comma-separated tags
            if (searchQuery.value.includes(',')) {
                addMultipleTags(searchQuery.value);
            } else {
                selectTag(searchQuery.value.trim());
            }
        }
        return;
    }

    if (event.key === "ArrowDown") {
        event.preventDefault();
        const totalOptions =
            availableTags.value.length + (canCreateNew.value ? 1 : 0);
        highlightedIndex.value = Math.min(
            highlightedIndex.value + 1,
            totalOptions - 1
        );
        return;
    }

    if (event.key === "ArrowUp") {
        event.preventDefault();
        highlightedIndex.value = Math.max(highlightedIndex.value - 1, -1);
        return;
    }

    if (event.key === "Escape") {
        showDropdown.value = false;
        searchInput.value?.blur();
        return;
    }
}

function onBlur() {
    setTimeout(() => (showDropdown.value = false), 150);
}

async function searchTags(query) {
    try {
        const response = await axios.get(
            route("tags.search", { type: props.tagType, query })
        );
        const tags = response.data.tags;

        // merge with existing tags (avoid duplicates)
        const newTags = tags.map((tagJson) => tagJson.tag);
        const newCounts = Object.fromEntries(
            tags.map((tagJson) => [tagJson.tag, tagJson.count])
        );

        allTags.value = [...new Set([...allTags.value, ...newTags])];
        tagCounts.value = { ...tagCounts.value, ...newCounts };
    } catch (error) {
        console.warn("Cannot query server for tags", error);
    } finally {
        isLoading.value = false;
    }
}

// load popular tags on mount
onMounted(async () => {
    try {
        const response = await axios.get(
            route("tags.search", { type: props.tagType, query: "" })
        );
        const tags = response.data.tags;

        allTags.value = tags.map((tagJson) => tagJson.tag);
        tagCounts.value = Object.fromEntries(
            tags.map((tagJson) => [tagJson.tag, tagJson.count])
        );
    } catch (error) {
        console.warn("Cannot prefetch tags", error);
    }
});
</script>
<template>
    <div class="mb-2">
        <!-- list of selected tags -->
        <div class="mb-2 flex flex-wrap" v-if="selectedTags.length > 0">
            <Tag
                v-for="tag in selectedTags"
                :key="tag"
                :tag="tag"
                variant="selected"
                removable
                @remove="removeTag"
                class="mr-2 my-1"
            />
        </div>

        <!-- search input container -->
        <div class="relative">
            <div class="relative flex items-center">
                <input
                    ref="searchInput"
                    v-model="searchQuery"
                    @input="onInput"
                    @keydown="onKeydown"
                    @focus="showDropdown = true"
                    @blur="onBlur"
                    placeholder="Add tags..."
                    class="w-full px-3 py-2 pr-10 text-md border border-gray-400 dark:border-gray-600 rounded-lg focus:outline-none placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary dark:bg-black transition-all"
                    :class="{
                        'rounded-b-none border-b-0':
                            showDropdown &&
                            (availableTags.length > 0 || canCreateNew || isLoading || searchQuery.trim()),
                    }"
                />
                <Icon
                    icon="mdi:information-outline"
                    class="absolute right-3 size-5 text-gray-400 dark:text-gray-500 cursor-help"
                    v-tooltip.top="`'everything' tag covers all possible tags`"
                />
            </div>

            <!-- dropdown rendered in body using teleport -->
            <teleport to="body">
                <div
                    v-show="
                        showDropdown &&
                        (availableTags.length > 0 || canCreateNew || isLoading || searchQuery.trim())
                    "
                    class="z-[9999] bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-800 border-t-0 rounded-b-lg shadow-lg max-h-60 overflow-y-auto"
                    :style="dropdownStyles"
                >
                    <!-- loading state -->
                    <div
                        v-if="isLoading"
                        class="flex items-center justify-center px-2 py-3"
                    >
                        <div class="flex items-center text-primary">
                            <Icon
                                icon="mdi:loading"
                                class="animate-spin -ml-1 mr-3 h-4 w-4 text-primaryDark"
                            />
                            <span class="text-sm">searching...</span>
                        </div>
                    </div>

                    <!-- available tags -->
                    <template v-else>
                        <!-- No results message -->
                        <div
                            v-if="availableTags.length === 0 && !canCreateNew"
                            class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center"
                        >
                            <Icon icon="mdi:information-outline" class="inline-block w-4 h-4 mr-1" />
                            <span v-if="searchQuery.trim()">
                                No tags found matching "{{ searchQuery.trim() }}"
                            </span>
                            <span v-else-if="props.mode === 'search'">
                                No tags available
                            </span>
                        </div>

                        <div
                            v-for="(tag, index) in availableTags"
                            :key="tag.name"
                            @mousedown.prevent="selectTag(tag.name)"
                            @mouseenter="highlightedIndex = index"
                            class="flex items-center justify-between px-3 py-1.5 cursor-pointer transition-colors"
                            :class="{
                                'bg-secondary text-primaryDark dark:bg-gray-800 dark:text-primaryLight': highlightedIndex === index,
                                'hover:bg-gray-50 dark:hover:bg-gray-900': highlightedIndex !== index,
                                'bg-orange-50 dark:bg-orange-900/20 border-l-2 border-orange-400': tag.name === 'everything' && highlightedIndex !== index,
                                'bg-orange-100 dark:bg-orange-900/30 border-l-2 border-orange-500': tag.name === 'everything' && highlightedIndex === index,
                            }"
                        >
                            <Tag
                                :tag="tag.name"
                                :variant="highlightedIndex === index ? 'highlighted' : 'default'"
                                :count="tag.count || null"
                            />
                        </div>

                        <!-- create new tag option -->
                        <div
                            v-if="canCreateNew"
                            @mousedown.prevent="selectTag(searchQuery.trim())"
                            @mouseenter="highlightedIndex = availableTags.length"
                            class="flex items-center px-4 py-3 cursor-pointer transition-colors"
                            :class="{
                                'bg-secondary text-primaryDark dark:bg-gray-800 dark:text-primaryLight': highlightedIndex === availableTags.length,
                                'hover:bg-gray-50 dark:hover:bg-gray-900': highlightedIndex !== availableTags.length,
                            }"
                        >
                            <Icon
                                class="w-4 h-4 text-green-500"
                                :icon="'mdi:add'"
                            ></Icon>
                            <span class="text-sm text-gray-700 dark:text-gray-100">
                                create "<strong>{{
                                    sanitizeTag(searchQuery.trim())
                                }}</strong
                                >"
                            </span>
                        </div>
                    </template>
                </div>
            </teleport>
        </div>
    </div>
</template>
