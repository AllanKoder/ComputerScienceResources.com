<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    resourceId: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <div class="w-full">
        <div class="relative">
            <ul
                class="flex flex-wrap px-1.5 py-1.5 list-none rounded-md bg-slate-100"
                role="list"
            >
                <li class="flex-auto text-center">
                    <Link
                        class="flex items-center justify-center w-full px-0 py-2 text-sm mb-0 transition-all ease-in-out border-0 rounded-md cursor-pointer text-slate-600 bg-inherit"
                        :class="{ 'bg-slate-200': $page.component.startsWith('Resources') }"
                        :href="route('resources.show', { id: resourceId })"
                    >
                        Resource
                    </Link>
                </li>
                <li class="flex-auto text-center">
                    <Link
                        class="flex items-center justify-center w-full px-0 py-2 text-sm mb-0 transition-all ease-in-out border-0 rounded-md cursor-pointer text-slate-600 bg-inherit"
                        :class="{ 'bg-slate-200': $page.component.startsWith('ResourceEdits') }"
                        :href="
                            route('resource_edits.index', { id: resourceId })
                        "
                    >
                        Propose Edits
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>
