<script setup>
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import ResourceItem from "@/Components/Resources/ResourceItem.vue";
import { ref } from "vue";
import Tag from "primevue/tag";

defineProps({
    resource: Object,
});

const upvote = () => emit("upvote", props.resource);
const downvote = () => emit("downvote", props.resource);
</script>

<template>
    <AppLayout title="Resource">
        <main class="py-12 flex justify-center">
            <div class="max-w-[110rem] mx-auto sm:px-6 lg:px-10 bg-white">
                <div class="flex gap-4">
                    <ResourceItem
                        :resource="resource"
                        @upvote="upvote"
                        @downvote="downvote"
                    />
                </div>
            </div>
        </main>
    </AppLayout>
</template>
