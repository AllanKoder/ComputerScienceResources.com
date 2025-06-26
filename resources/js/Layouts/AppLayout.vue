<script setup>
import { Head } from "@inertiajs/vue3";
import Banner from "@/Components/Banner.vue";
import ToastHandler from "@/Components/ToastHandler.vue";
import Navbar from "@/Components/Navigation/Navbar.vue";

defineProps({
    title: String,
});
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <!-- Handle Toasts -->
        <ToastHandler />

        <div class="min-h-screen bg-background dark:bg-gray-900">
            <Navbar />

            <!-- Page Heading -->
            <header
                v-if="$slots.header"
                class="bg-secondary dark:bg-secondaryDark shadow"
            >
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
