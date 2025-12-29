<script setup>
import { Icon } from "@iconify/vue";
import BackButton from "@/Components/Navigation/BackButton.vue";
import UserProfile from "@/Components/Profile/UserProfile.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps({
    editedResource: {
        type: Object,
        required: true,
    },
    originalResource: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['merge']);
</script>

<template>
    <div>
        <div class="my-2 flex flex-row gap-2">
            <BackButton
                :route="
                    route('resources.show', {
                        slug: originalResource.slug,
                        tab: 'edits',
                    })
                "
            >
            <span class="my-auto">Back to {{ originalResource.name }}</span>
            </BackButton>
        </div>
        <div class="border-b border-gray-200 dark:border-gray-800 pb-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <h1
                            class="text-3xl font-bold text-gray-900 dark:text-gray-100"
                        >
                            {{ editedResource.edit_title }}
                        </h1>
                    </div>
                    <p
                        class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed mb-4"
                    >
                        {{ editedResource.edit_description }}
                    </p>

                    <UserProfile
                        :user="editedResource.user"
                        :date="editedResource.created_at"
                    />
                </div>
                <div
                    v-if="editedResource.can_merge_edits"
                    class="ml-6"
                >
                    <PrimaryButton
                        @click="emit('merge', editedResource.id)"
                    >
                        <Icon
                            icon="mdi:source-merge"
                            class="w-4 h-4 mr-2"
                        />
                        Merge Changes
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </div>
</template>
