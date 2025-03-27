<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { computed, ref } from "vue";
import Tag from "primevue/tag";
import Button from "primevue/button";
import { pricingLabels, difficultyLabels } from "@/Helpers/constants.js";

const props = defineProps({
    originalResource: {
        type: Object,
        required: true,
    },
    editedResource: {
        type: Object,
        required: true,
    },
    resourceId: {
        type: Number,
        required: true,
    }
});

const emit = defineEmits(['approveChanges', 'rejectChanges']);

const platformColors = {
    book: "blue",
    podcast: "green",
    youtube_channel: "red",
    blog: "orange",
    website: "purple",
    organization: "cyan",
    bootcamp: "pink",
    newsletter: "indigo",
    workshop: "teal",
    course: "yellow",
    forum: "gray",
    mobile_app: "lime",
    desktop_app: "amber",
    magazine: "rose",
};

// Helper function to parse tags safely
const parseTags = (tags) => {
    // If tags is already an array, return it
    if (Array.isArray(tags)) return tags;
    
    // If tags is a string, try parsing
    if (typeof tags === 'string') {
        try {
            // First, try parsing as JSON
            return JSON.parse(tags);
        } catch {
            // If JSON parsing fails, split by comma and trim
            return tags.replace(/[\[\]"]/g, '').split(',').map(tag => tag.trim());
        }
    }
    
    // If all else fails, return an empty array
    return [];
};

const originalPlatformList = computed(() => 
    props.originalResource.platforms.split(",").map((platform) => platform.trim())
);

const editedPlatformList = computed(() => 
    props.editedResource.platforms.split(",").map((platform) => platform.trim())
);

const originalTopicTags = computed(() => parseTags(props.originalResource.topic_tags));
const originalProgrammingTags = computed(() => parseTags(props.originalResource.programming_language_tags));
const originalGeneralTags = computed(() => parseTags(props.originalResource.general_tags));

const compareFields = [
    { 
        key: 'name', 
        label: 'Name',
        type: 'text'
    },
    { 
        key: 'description', 
        label: 'Description',
        type: 'textarea'
    },
    { 
        key: 'page_url', 
        label: 'Resource URL',
        type: 'url'
    },
    { 
        key: 'difficulty', 
        label: 'Difficulty',
        type: 'select',
        formatter: (value) => difficultyLabels[value]
    },
    { 
        key: 'pricing', 
        label: 'Pricing',
        type: 'select',
        formatter: (value) => pricingLabels[value]
    }
];

const hasChanges = computed(() => {
    return compareFields.some(field => 
        props.originalResource[field.key] !== props.editedResource[field.key]
    ) ||
    JSON.stringify(originalPlatformList.value) !== JSON.stringify(editedPlatformList.value) ||
    JSON.stringify(originalTopicTags.value) !== JSON.stringify(props.editedResource.topic_tags) ||
    JSON.stringify(originalProgrammingTags.value) !== JSON.stringify(props.editedResource.programming_language_tags) ||
    JSON.stringify(originalGeneralTags.value) !== JSON.stringify(props.editedResource.general_tags);
});

const handleApprove = () => {
    emit('approveChanges', props.resourceId);
};

const handleReject = () => {
    emit('rejectChanges', props.resourceId);
};
</script>

<template>
    <AppLayout :title="`Compare Versions: ${props.originalResource.name}`">
        <Head :title="`Compare Versions: ${props.originalResource.name}`" />
        
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h1 class="text-2xl font-bold mb-6">Resource Version Comparison</h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Original Version -->
                        <div class="border rounded-lg p-4">
                            <h2 class="text-xl font-semibold mb-4 text-gray-700">Original Version</h2>
                            
                            <!-- Comparison Fields -->
                            <div v-for="field in compareFields" :key="field.key" class="mb-4">
                                <div class="font-semibold text-gray-600">{{ field.label }}:</div>
                                <div class="text-gray-800">
                                    {{ field.formatter 
                                        ? field.formatter(props.originalResource[field.key])
                                        : props.originalResource[field.key] 
                                    }}
                                </div>
                            </div>

                            <!-- Platforms -->
                            <div class="mb-4">
                                <div class="font-semibold text-gray-600 mb-2">Platforms:</div>
                                <div class="flex flex-wrap gap-2">
                                    <Tag
                                        v-for="platform in originalPlatformList"
                                        :key="platform"
                                        :value="platform"
                                        :severity="platformColors[platform]"
                                        class="capitalize"
                                    />
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="mb-4">
                                <div class="font-semibold text-gray-600 mb-2">Tags:</div>
                                <div class="flex flex-wrap gap-1">
                                    <Tag
                                        v-for="tag in originalTopicTags"
                                        :key="tag"
                                        :value="tag"
                                        severity="info"
                                        class="text-xs mr-1 mb-1"
                                    />
                                    <Tag
                                        v-for="tag in originalProgrammingTags"
                                        :key="tag"
                                        :value="tag"
                                        severity="success"
                                        class="text-xs mr-1 mb-1"
                                    />
                                    <Tag
                                        v-for="tag in originalGeneralTags"
                                        :key="tag"
                                        :value="tag"
                                        severity="warning"
                                        class="text-xs mr-1 mb-1"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Edited Version -->
                        <div class="border rounded-lg p-4">
                            <h2 class="text-xl font-semibold mb-4 text-gray-700">Edited Version</h2>
                            
                            <!-- Comparison Fields -->
                            <div v-for="field in compareFields" :key="field.key" class="mb-4">
                                <div class="font-semibold text-gray-600">{{ field.label }}:</div>
                                <div 
                                    class="text-gray-800"
                                    :class="{
                                        'bg-yellow-50': props.originalResource[field.key] !== props.editedResource[field.key],
                                        'p-1 rounded': props.originalResource[field.key] !== props.editedResource[field.key]
                                    }"
                                >
                                    {{ field.formatter 
                                        ? field.formatter(props.editedResource[field.key])
                                        : props.editedResource[field.key] 
                                    }}
                                </div>
                            </div>

                            <!-- Platforms -->
                            <div class="mb-4">
                                <div class="font-semibold text-gray-600 mb-2">Platforms:</div>
                                <div class="flex flex-wrap gap-2">
                                    <Tag
                                        v-for="platform in editedPlatformList"
                                        :key="platform"
                                        :value="platform"
                                        :severity="platformColors[platform]"
                                        class="capitalize"
                                        :class="{
                                            'bg-yellow-50': !originalPlatformList.includes(platform)
                                        }"
                                    />
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="mb-4">
                                <div class="font-semibold text-gray-600 mb-2">Tags:</div>
                                <div class="flex flex-wrap gap-1">
                                    <Tag
                                        v-for="tag in props.editedResource.topic_tags"
                                        :key="tag"
                                        :value="tag"
                                        severity="info"
                                        class="text-xs mr-1 mb-1"
                                        :class="{
                                            'bg-yellow-50': !originalTopicTags.includes(tag)
                                        }"
                                    />
                                    <Tag
                                        v-for="tag in props.editedResource.programming_language_tags"
                                        :key="tag"
                                        :value="tag"
                                        severity="success"
                                        class="text-xs mr-1 mb-1"
                                        :class="{
                                            'bg-yellow-50': !originalProgrammingTags.includes(tag)
                                        }"
                                    />
                                    <Tag
                                        v-for="tag in props.editedResource.general_tags"
                                        :key="tag"
                                        :value="tag"
                                        severity="warning"
                                        class="text-xs mr-1 mb-1"
                                        :class="{
                                            'bg-yellow-50': !originalGeneralTags.includes(tag)
                                        }"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Actions -->
                    <div class="mt-6 flex justify-end space-x-4">
                        <Button 
                            label="Reject Changes" 
                            severity="danger" 
                            @click="handleReject"
                            :disabled="!hasChanges"
                        />
                        <Button 
                            label="Approve Changes" 
                            severity="success" 
                            @click="handleApprove"
                            :disabled="!hasChanges"
                        />
                    </div>
                </div>
            </div>
        </main>
    </AppLayout>
</template>