<script setup>
import { Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Icon } from '@iconify/vue';

const props = defineProps({
    resourceId: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <div class="flex justify-end mb-4">
        <Link :href="route('resource_edits.create', { computerScienceResource: props.resourceId })">
            <PrimaryButton class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                <Icon icon="mdi:pencil" class="w-5 h-5" />
                <span>Propose Edits</span>
            </PrimaryButton>
        </Link>
    </div>
</template>
