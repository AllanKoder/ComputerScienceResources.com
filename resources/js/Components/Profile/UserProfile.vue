<script setup>
import ProfilePhoto from "@/Components/Profile/ProfilePhoto.vue";

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

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
</script>

<template>
    <div class="flex items-center space-x-4">
        <ProfilePhoto
            v-if="user"
            :src="user?.profile_photo_url"
            :alt="'User Avator'"
        />
        <div class="min-w-0">
            <p class="text-sm text-gray-800 truncate">
                {{ user?.name ?? "Deleted User" }}
            </p>
            <time
                :datetime="date"
                class="text-sm text-gray-500"
                :title="date"
            >
                {{ formatDate(date) }}
            </time>
        </div>
    </div>
</template>
