<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import { Icon } from "@iconify/vue";
import UserDropdown from "@/Components/Navbar/UserDropdown.vue";
import NavLinkDropdown from "@/Components/NavLinkDropdown.vue";
// import NavLink from "@/Components/NavLink.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ApplicationHeaderLogo from "@/Components/ApplicationHeaderLogo.vue";
import { useDarkMode } from "@/Composables/useDarkMode.js";

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route("logout"));
};

const { isDark, toggleDark } = useDarkMode();
</script>

<template>
    <nav
        class="bg-white dark:bg-gray-900 border-accent dark:border-accent-dark"
    >
        <!-- Primary Navigation Menu -->
        <div class="w-full max-w-screen-2xl mx-auto px-4 md:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <Link :href="route('home')">
                            <ApplicationHeaderLogo/>
                        </Link>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 xl:-my-px xl:ms-10 xl:flex">
                        <NavLinkDropdown
                            label="About"
                            :active="route().current('about') || route().current('rules')"
                        >
                            <DropdownLink :href="route('about')">
                                <Icon icon="mdi:information-outline" class="mr-2" />
                                About Us
                            </DropdownLink>

                            <div class="border-t border-gray-200 dark:border-gray-600" />

                            <DropdownLink :href="route('rules')">
                                <Icon icon="mdi:gavel" class="mr-2" />
                                Rules
                            </DropdownLink>
                        </NavLinkDropdown>

                        <NavLinkDropdown
                            label="Resources"
                            :active="route().current('resources.index') || route().current('resource_edits.index')"
                        >
                            <DropdownLink :href="route('resources.index')">
                                <Icon icon="mdi:magnify" class="mr-2" />
                                Browse Resources
                            </DropdownLink>

                            <div class="border-t border-gray-200 dark:border-gray-600" />

                            <DropdownLink :href="route('resource_edits.index')">
                                <Icon icon="mdi:pencil-box-multiple" class="mr-2" />
                                Resource Edits
                            </DropdownLink>
                        </NavLinkDropdown>
                    </div>
                </div>

                <!-- Authenticated -->
                <div class="hidden xl:flex xl:items-center xl:ms-6">


                        <!-- GitHub link button -->
                        <a
                        href="https://github.com/AllanKoder/ComputerScienceResources.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mx-2 p-2 rounded-full hover:bg-accent/30 dark:hover:bg-primaryDark/30 focus:outline-none text-primaryDark dark:text-primary"
                        >
                        <Icon
                        icon="mdi:github"
                        class="size-6"
                        />
                    </a>

                    <!-- Dark mode icon button -->
                    <button
                    @click="toggleDark"
                    type="button"
                    class="mx-2 p-2 rounded-full hover:bg-accent/30 dark:hover:bg-primaryDark/30 focus:outline-none text-primaryDark dark:text-primary"
                    >
                    <Icon
                            v-if="!isDark"
                            icon="mdi:weather-night"
                            class="size-6"
                            />
                            <Icon
                            v-else
                            icon="mdi:white-balance-sunny"
                            class="size-6"
                            />
                        </button>

                    <template v-if="$page.props.auth.user">
                        <!-- Create Resource Button -->
                        <Link :href="route('resources.create')">
                            <SecondaryButton
                                class="dark:bg-primary dark:text-white dark:hover:bg-primary"
                            >
                                <Icon icon="mdi:plus" class="mr-2" />
                                Post a Resource
                            </SecondaryButton>
                        </Link>

                        <!-- Settings Dropdown -->
                        <div class="flex items-center justify-center">
                            <UserDropdown align="right" width="48" />
                        </div>
                    </template>
                    <!-- Guest -->
                    <template v-else>
                        <div class="hidden xl:flex xl:items-center xl:ms-6">
                            <Link :href="route('login')">
                                <SecondaryButton
                                    class="dark:bg-primary dark:text-white dark:hover:bg-primary"
                                >
                                    <Icon icon="mdi:login" class="mr-2" />
                                    Sign In
                                </SecondaryButton>
                            </Link>
                        </div>
                    </template>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center xl:hidden">
                    <button
                        class="inline-flex items-center justify-center p-2 rounded-md text-primaryDark dark:text-primary hover:text-primary dark:hover:text-primaryLight hover:bg-accent/30 dark:hover:bg-primaryDark/30 focus:outline-none focus:bg-accent/30 dark:focus:bg-primaryDark/30 focus:text-primary dark:focus:text-primaryLight transition duration-150 ease-in-out"
                        @click="
                            showingNavigationDropdown =
                                !showingNavigationDropdown
                        "
                    >
                        <svg
                            class="size-6"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
            :class="{
                block: showingNavigationDropdown,
                hidden: !showingNavigationDropdown,
            }"
            class="xl:hidden bg-white dark:bg-gray-900 border-t border-accent dark:border-accent-dark"
        >
            <div class="pt-2 pb-3 space-y-2">
                <div class="flex items-center gap-2 px-2">
                    <!-- Dark mode icon button -->
                    <button
                        @click="toggleDark"
                        type="button"
                        class="p-2 rounded-full hover:bg-accent/30 dark:hover:bg-primaryDark/30 focus:outline-none text-primaryDark dark:text-primary"
                    >
                        <Icon
                            v-if="!isDark"
                            icon="mdi:weather-night"
                            class="size-6"
                        />
                        <Icon
                            v-else
                            icon="mdi:white-balance-sunny"
                            class="size-6"
                        />
                    </button>
                </div>

                <ResponsiveNavLink
                    href="https://github.com/AllanKoder/ComputerScienceResources.com"
                    class="text-primaryDark dark:text-primary hover:text-primary dark:hover:text-primaryLight"
                    as="a"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <Icon icon="mdi:github" class="mr-2" />
                    GitHub
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    :href="route('about')"
                    :active="route().current('about')"
                    class="text-primaryDark dark:text-primary hover:text-primary dark:hover:text-primaryLight"
                >
                    About Us
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    :href="route('rules')"
                    :active="route().current('rules')"
                    class="text-primaryDark dark:text-primary hover:text-primary dark:hover:text-primaryLight"
                >
                    Rules
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    :href="route('resources.index')"
                    :active="route().current('resources.index')"
                    class="text-primaryDark dark:text-primary hover:text-primary dark:hover:text-primaryLight"
                >
                    <Icon icon="mdi:magnify" class="mr-2" />
                    Browse Resources
                </ResponsiveNavLink>

                <ResponsiveNavLink
                    :href="route('resource_edits.index')"
                    :active="route().current('resource_edits.index')"
                    class="text-primaryDark dark:text-primary hover:text-primary dark:hover:text-primaryLight"
                >
                    <Icon icon="mdi:pencil-box-multiple" class="mr-2" />
                    Resource Edits
                </ResponsiveNavLink>

                <!-- Authenticated -->
                <template v-if="$page.props.auth.user">
                    <ResponsiveNavLink
                        :href="route('resources.create')"
                        class="inline-flex items-center border border-primary dark:border-primaryLight rounded-md font-semibold text-xs text-primary dark:text-primaryLight uppercase tracking-widest hover:bg-primary hover:text-white dark:hover:bg-primaryLight dark:hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-accent dark:focus:ring-primaryLight focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <Icon icon="mdi:plus" class="mr-2" />
                        Post a Resource
                    </ResponsiveNavLink>
                </template>
                <!-- Guest -->
                <template v-else>
                    <ResponsiveNavLink
                        :href="route('login')"
                        class="ml-2 inline-flex items-center border border-primary dark:border-primaryLight rounded-md font-semibold text-xs text-primary dark:text-primaryLight uppercase tracking-widest hover:bg-primary hover:text-white dark:hover:bg-primaryLight dark:hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-accent dark:focus:ring-primaryLight focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <span class="text-primary dark:text-primaryLight">
                            <Icon icon="mdi:login" class="mr-2" />
                            Sign In
                        </span>
                    </ResponsiveNavLink>
                </template>
            </div>

            <!-- Responsive Settings Options -->
            <template v-if="$page.props.auth.user">
                <div
                    class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700"
                >
                    <div class="flex items-center px-4">
                        <div
                            v-if="$page.props.jetstream.managesProfilePhotos"
                            class="shrink-0 me-3"
                        >
                            <img
                                class="size-10 rounded-full object-cover"
                                :src="$page.props.auth.user.profile_photo_url"
                                :alt="$page.props.auth.user.name"
                                is="ProfilePhoto"
                            />
                        </div>

                        <div>
                            <div
                                class="font-medium text-base text-gray-800 dark:text-gray-200"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div
                                class="font-medium text-sm text-gray-500 dark:text-gray-400"
                            >
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('profile.show')"
                            :active="route().current('profile.show')"
                        >
                            Profile
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            v-if="$page.props.jetstream.hasApiFeatures"
                            :href="route('api-tokens.index')"
                            :active="route().current('api-tokens.index')"
                        >
                            API Tokens
                        </ResponsiveNavLink>

                        <!-- Authentication -->
                        <form method="POST" @submit.prevent="logout">
                            <ResponsiveNavLink as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </nav>
</template>
