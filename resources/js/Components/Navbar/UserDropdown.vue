<script setup>
import { router } from "@inertiajs/vue3";
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Icon } from '@iconify/vue';
import { useDarkMode } from '@/Composables/useDarkMode.js';

const props = defineProps({
    align: {
        default: 'right',
    },
    width: {
        default: '48',
    },
    contentClasses: {
        default: () => ['py-1', 'bg-white dark:bg-gray-700'],
    },
});

const logout = () => {
    router.post(route('logout'));
};

const { isDark, toggleDark } = useDarkMode();
</script>

<template>
    <div class="flex items-center justify-center">
        <div class="relative">
            <Dropdown :align="align" :width="width" :content-classes="contentClasses">
                <template #trigger>
                    <button
                        v-if="$page.props.jetstream.managesProfilePhotos"
                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                    >
                        <img
                            class="size-8 rounded-full object-cover"
                            :src="$page.props.auth.user.profile_photo_url"
                            :alt="$page.props.auth.user.name"
                        />
                    </button>

                    <span v-else class="inline-flex rounded-md">
                        <button
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                        >
                            {{ $page.props.auth.user.name }}

                            <svg
                                class="ms-2 -me-0.5 size-4"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                />
                            </svg>
                        </button>
                    </span>
                </template>

                <template #content>
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        Manage Account
                    </div>

                    <DropdownLink :href="route('profile.show')">
                        Profile
                    </DropdownLink>

                    <!-- Dark mode toggle -->
                    <button
                        @click="toggleDark"
                        class="w-full flex items-center gap-2 px-4 py-2 text-sm rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none transition"
                        :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                        type="button"
                    >
                        <Icon v-if="!isDark" icon="mdi:weather-night" class="size-5" />
                        <Icon v-else icon="mdi:white-balance-sunny" class="size-5" />
                        <span>{{ isDark ? 'Light Mode' : 'Dark Mode' }}</span>
                    </button>

                    <DropdownLink
                        v-if="$page.props.jetstream.hasApiFeatures"
                        :href="route('api-tokens.index')"
                    >
                        API Tokens
                    </DropdownLink>

                    <div class="border-t border-gray-200 dark:border-gray-600" />

                    <!-- Authentication -->
                    <form @submit.prevent="logout">
                        <DropdownLink as="button">
                            Log Out
                        </DropdownLink>
                    </form>
                </template>
            </Dropdown>
        </div>
    </div>
</template>
