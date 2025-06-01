<script setup>
import { onMounted, ref } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import Banner from "@/Components/Banner.vue";
import { Icon } from "@iconify/vue";
import UserDropdown from "@/Components/Navbar/UserDropdown.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { useToast } from "primevue/usetoast";
import SecondaryButton from "@/Components/SecondaryButton.vue";

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route("logout"));
};

// Handling toast messages
const flash = usePage().props.flash;
const toast = useToast();
onMounted(() => {
    if (!flash) return;

    const TOAST_LIFETIME = 10_000; // miliseconds
    if (flash.success) {
        toast.add({
            severity: "success",
            summary: "Success",
            detail: flash.success,
            life: TOAST_LIFETIME,
        });
    } else if (flash.warning) {
        toast.add({
            severity: "warn",
            summary: "Warning",
            detail: flash.warning,
            life: TOAST_LIFETIME,
        });
    } else if (flash.error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: flash.error,
            life: TOAST_LIFETIME,
        });
    }
});
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <!-- Handle Toasts -->
        <Toast />

        <div class="min-h-screen bg-background dark:bg-gray-900">
            <nav
                class="bg-white dark:bg-secondaryDark border-accent dark:border-primaryDark"
            >
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('resources.index')">
                                    <ApplicationMark class="block h-9 w-auto" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('resources.index')"
                                    :active="route().current('resources.index')"
                                    class="text-primaryDark hover:text-primary"
                                >
                                    Resources
                                </NavLink>
                            </div>
                        </div>

                        <!-- Authenticated -->
                        <template v-if="$page.props.auth.user">
                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <!-- Create Resource Button -->
                                <Link :href="route('resources.create')">
                                    <SecondaryButton>
                                        <Icon icon="mdi:plus" class="mr-2" />
                                        Create
                                    </SecondaryButton>
                                </Link>

                                <!-- Settings Dropdown -->
                                <div class="flex items-center justify-center">
                                    <UserDropdown align="right" width="48" />
                                </div>
                            </div>
                        </template>

                        <!-- Guest -->
                        <template v-else>
                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <Link :href="route('login')">
                                    <SecondaryButton>
                                        <Icon icon="mdi:login" class="mr-2" />
                                        Sign In
                                    </SecondaryButton>
                                </Link>
                            </div>
                        </template>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-primaryDark dark:text-primary hover:text-primary dark:hover:text-primaryDark hover:bg-accent/30 dark:hover:bg-primaryDark/30 focus:outline-none focus:bg-accent/30 dark:focus:bg-primaryDark/30 focus:text-primary dark:focus:text-primaryDark transition duration-150 ease-in-out"
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
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
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
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('resources.index')"
                            :active="route().current('resources.index')"
                            class="text-primaryDark hover:text-primary"
                        >
                            Resources
                        </ResponsiveNavLink>

                        <!-- Create Resource Button -->
                        <ResponsiveNavLink
                            :href="route('resources.create')"
                            class="mx-2 inline-flex items-center border border-primary rounded-md font-semibold text-xs text-primary uppercase tracking-widest hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            <Icon icon="mdi:plus" class="" />
                            Create
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <template v-if="$page.props.auth.user">
                        <div
                            class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600"
                        >
                            <div class="flex items-center px-4">
                                <div
                                    v-if="
                                        $page.props.jetstream
                                            .managesProfilePhotos
                                    "
                                    class="shrink-0 me-3"
                                >
                                    <img
                                        class="size-10 rounded-full object-cover"
                                        :src="
                                            $page.props.auth.user
                                                .profile_photo_url
                                        "
                                        :alt="$page.props.auth.user.name"
                                    />
                                </div>

                                <div>
                                    <div
                                        class="font-medium text-base text-gray-800 dark:text-gray-200"
                                    >
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <div
                                        class="font-medium text-sm text-gray-500"
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
                                    :active="
                                        route().current('api-tokens.index')
                                    "
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

            <!-- Page Heading -->
            <header
                v-if="$slots.header"
                class="bg-secondary dark:bg-secondaryDark shadow"
            >
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
