<script setup>
import ProfilePhoto from "@/Components/Profile/ProfilePhoto.vue";
import { formatDate } from "@/Helpers/dates";

defineProps({
    user: {
        type: Object,
        required: true,
    },
    date: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="flex items-center space-x-3">
        <ProfilePhoto
            v-if="user"
            :src="user?.profile_photo_url"
            :alt="'User Avator'"
        />
        <div class="min-w-0">
            <p class="text-sm text-gray-800 dark:text-gray-100 truncate">
                {{ user?.name ?? "Deleted User" }}
            </p>
            <time
                :datetime="date"
                class="text-sm text-gray-500 dark:text-gray-300"
                :title="date"
            >
                {{ formatDate(date) }}
            </time>
        </div>
    </div>
</template>
