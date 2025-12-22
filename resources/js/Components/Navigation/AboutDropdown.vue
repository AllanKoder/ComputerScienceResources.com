<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import Menu from 'primevue/menu';
import Button from 'primevue/button';

const menu = ref();

const items = ref([
    {
        label: 'About Us',
        icon: 'mdi:information-outline',
        command: () => {
            router.visit(route('about'));
        }
    },
    {
        label: 'Rules',
        icon: 'mdi:gavel',
        command: () => {
            router.visit(route('rules'));
        }
    }
]);

const toggle = (event) => {
    menu.value.toggle(event);
};

// Check if any about route is active
const isAboutActive = () => {
    return route().current('about') || route().current('rules');
};
</script>

<template>
    <div class="relative">
        <Button
            type="button"
            @click="toggle"
            :class="[
                'flex items-center px-1 pt-1 pb-1 h-16 text-sm font-medium border-b-2 transition duration-150 ease-in-out focus:outline-none',
                isAboutActive()
                    ? 'border-primary dark:border-primaryLight text-primary dark:text-primaryLight'
                    : 'border-transparent text-primaryDark dark:text-primary hover:text-primary dark:hover:text-primaryLight hover:border-gray-300 dark:hover:border-gray-700'
            ]"
            severity="secondary"
            text
            plain
        >
            About
            <Icon icon="mdi:chevron-down" class="ml-1 size-4" />
        </Button>

        <Menu
            ref="menu"
            :model="items"
            :popup="true"
            class="mt-1"
        >
            <template #item="{ item }">
                <div class="flex items-center px-3 py-2 cursor-pointer hover:bg-accent/30 dark:hover:bg-primaryDark/30">
                    <Icon :icon="item.icon" class="mr-2" />
                    <span>{{ item.label }}</span>
                </div>
            </template>
        </Menu>
    </div>
</template>
