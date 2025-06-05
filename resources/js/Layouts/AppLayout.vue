<script setup>
import { onMounted } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import Banner from "@/Components/Banner.vue";
import { useToast } from "primevue/usetoast";
import Navbar from "@/Components/Navigation/Navbar.vue";

defineProps({
    title: String,
});

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
