<template>
    <div class="mb-2">
        <!-- list of selected tags -->
        <div class="mb-2 flex flex-wrap" v-if="selectedtags.length > 0">
            <span
                v-for="tag in selectedtags"
                :key="tag"
                class="inline-flex items-center mr-2 my-1 bg-secondary text-primarydark px-3 py-1 rounded-full text-sm font-medium transition-colors"
            >
                <button
                    @click="removetag(tag)"
                    class="mr-2 text-primarydark"
                    type="button"
                >
                    <icon :icon="'mdi:close'" />
                </button>
                <span>{{ tag }}</span>
            </span>
        </div>

        <!-- search input container -->
        <div class="relative">
            <input
                ref="searchinput"
                v-model="searchquery"
                @input="oninput"
                @keydown="onkeydown"
                @focus="showdropdown = true"
                @blur="onblur"
                placeholder="add tags..."
                class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                :class="{
                    'rounded-b-none border-b-0':
                        showdropdown &&
                        (availabletags.length > 0 || cancreatenew || isloading),
                }"
            />

            <!-- dropdown -->
            <div
                v-show="
                    showdropdown &&
                    (availabletags.length > 0 || cancreatenew || isloading)
                "
                class="absolute z-50 w-full bg-white border border-gray-300 border-t-0 rounded-b-lg shadow-lg max-h-60 overflow-y-auto"
            >
                <!-- loading state -->
                <div
                    v-if="isloading"
                    class="flex items-center justify-center px-2 py-3"
                >
                    <div class="flex items-center text-primary">
                        <icon
                            icon="mdi:loading"
                            class="animate-spin -ml-1 mr-3 h-4 w-4 text-primarydark"
                        />
                        <span class="text-sm">searching...</span>
                    </div>
                </div>

                <!-- available tags -->
                <template v-else>
                    <div
                        v-for="(tag, index) in availabletags"
                        :key="tag.name"
                        @mousedown.prevent="selecttag(tag.name)"
                        @mouseenter="highlightedindex = index"
                        class="flex items-center justify-between px-4 py-3 cursor-pointer transition-colors"
                        :class="{
                            'bg-secondary text-primarydark':
                                highlightedindex === index,
                            'hover:bg-gray-50': highlightedindex !== index,
                        }"
                    >
                        <span class="text-sm">{{ tag.name }}</span>
                        <span
                            v-if="tag.count"
                            class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full"
                            :class="{
                                'bg-secondary text-primarydark':
                                    highlightedindex === index,
                            }"
                        >
                            {{ tag.count }}
                        </span>
                    </div>

                    <!-- create new tag option -->
                    <div
                        v-if="cancreatenew"
                        @mousedown.prevent="selecttag(searchquery.trim())"
                        @mouseenter="highlightedindex = availabletags.length"
                        class="flex items-center px-4 py-3 cursor-pointer transition-colors"
                        :class="{
                            'bg-secondary text-primarydark':
                                highlightedindex === availabletags.length,
                            'hover:bg-gray-50':
                                highlightedindex !== availabletags.length,
                        }"
                    >
                        <icon
                            class="w-4 h-4 text-green-500"
                            :icon="'mdi:add'"
                        ></icon>
                        <span class="text-sm text-gray-700">
                            create "<strong>{{
                                sanitizetag(searchquery.trim())
                            }}</strong
                            >"
                        </span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, nexttick, onmounted } from "vue";
import { defineModel } from "vue";
import axios from "axios";
import { icon } from "@iconify/vue";

const DEBOUNCE_TIME = 450; // miliseconds

const props = defineprops({
    tagtype: {
        type: string,
        required: true,
    },
});

const model = defineModel();

// simple state
const selectedtags = ref([]);
const searchquery = ref("");
const alltags = ref([]); // all tags from server (popular + search results)
const tagcounts = ref({});
const showdropdown = ref(false);
const highlightedindex = ref(-1);
const searchinput = ref(null);
const isloading = ref(false);

let searchtimeout = null;

// computed properties
const availabletags = computed(() => {
    const query = searchquery.value.trim().tolowercase();

    return alltags.value
        .filter((tagname) => {
            // don't show already selected tags
            if (selectedtags.value.includes(tagname)) return false;

            // if no query, show all
            if (!query) return true;

            // filter by query
            return tagname.tolowercase().includes(query);
        })
        .map((tagname) => ({
            name: tagname,
            count: tagcounts.value[tagname] || 0,
        }));
});

const cancreatenew = computed(() => {
    const query = searchquery.value.trim();
    if (!query || isloading.value) return false;

    const sanitized = sanitizetag(query);
    return (
        !selectedtags.value.includes(sanitized) &&
        !alltags.value.some(
            (tag) => tag.tolowercase() === sanitized.tolowercase()
        )
    );
});

// sync model
watch(
    () => model.value,
    (newval) => {
        if (array.isarray(newval)) {
            selectedtags.value = [...newval];
        }
    },
    { immediate: true }
);

function sanitizetag(tag) {
    let transformedtag = tag.trim().tolowercase();
    transformedtag = transformedtag.replace(/\s+/g, "-");
    transformedtag = transformedtag.replace(/[^a-z0-9+#.-]/g, "");
    return transformedtag;
}

function addtag(tag) {
    if (!tag) return;

    const transformedtag = sanitizetag(tag);
    if (!selectedtags.value.includes(transformedtag)) {
        selectedtags.value.push(transformedtag);
        model.value = [...selectedtags.value];
    }
}

function removetag(tag) {
    selectedtags.value = selectedtags.value.filter((t) => t !== tag);
    model.value = [...selectedtags.value];
}

function selecttag(tag) {
    addtag(tag);
    searchquery.value = "";
    showdropdown.value = false;
    nexttick(() => searchinput.value?.focus());
}

function oninput() {
    showdropdown.value = true;
    highlightedindex.value = -1;

    // clear previous timeout
    cleartimeout(searchtimeout);

    const query = searchquery.value.trim();
    if (query) {
        isloading.value = true;
        searchtimeout = settimeout(() => searchtags(query), DEBOUNCE_TIME);
    }
}

function onkeydown(event) {
    if (event.key === "enter") {
        event.preventdefault();

        if (
            highlightedindex.value >= 0 &&
            highlightedindex.value < availabletags.value.length
        ) {
            selecttag(availabletags.value[highlightedindex.value].name);
        } else if (
            highlightedindex.value === availabletags.value.length &&
            cancreatenew.value
        ) {
            selecttag(searchquery.value.trim());
        } else if (searchquery.value.trim()) {
            selecttag(searchquery.value.trim());
        }
        return;
    }

    if (event.key === "arrowdown") {
        event.preventdefault();
        const totaloptions =
            availabletags.value.length + (cancreatenew.value ? 1 : 0);
        highlightedindex.value = math.min(
            highlightedindex.value + 1,
            totaloptions - 1
        );
        return;
    }

    if (event.key === "arrowup") {
        event.preventdefault();
        highlightedindex.value = math.max(highlightedindex.value - 1, -1);
        return;
    }

    if (event.key === "escape") {
        showdropdown.value = false;
        searchinput.value?.blur();
        return;
    }

    if (
        event.key === "backspace" &&
        !searchquery.value &&
        selectedtags.value.length > 0
    ) {
        removetag(selectedtags.value[selectedtags.value.length - 1]);
    }
}

function onblur() {
    settimeout(() => (showdropdown.value = false), 150);
}

async function searchtags(query) {
    try {
        const response = await axios.get(
            route("tags.search", { type: props.tagtype, query })
        );
        const tags = response.data.tags;

        // merge with existing tags (avoid duplicates)
        const newtags = tags.map((tagjson) => tagjson.tag);
        const newcounts = object.fromentries(
            tags.map((tagjson) => [tagjson.tag, tagjson.count])
        );

        alltags.value = [...new set([...alltags.value, ...newtags])];
        tagcounts.value = { ...tagcounts.value, ...newcounts };
    } catch (error) {
        console.warn("cannot query server for tags", error);
    } finally {
        isloading.value = false;
    }
}

// load popular tags on mount
onmounted(async () => {
    try {
        const response = await axios.get(
            route("tags.search", { type: props.tagtype, query: "" })
        );
        const tags = response.data.tags;

        alltags.value = tags.map((tagjson) => tagjson.tag);
        tagcounts.value = object.fromentries(
            tags.map((tagjson) => [tagjson.tag, tagjson.count])
        );
    } catch (error) {
        console.warn("cannot prefetch tags", error);
    }
});
</script>
