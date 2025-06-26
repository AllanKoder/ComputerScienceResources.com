<script setup>
import { watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";

// Handling toast messages
const page = usePage();
const toast = useToast();

watch(
    () => page.props.flash,
    (flash) => {
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
        }
    },
    { deep: true },
);
</script>

<template>
    <Toast />
</template>
