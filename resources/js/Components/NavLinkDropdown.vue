<script setup>
import { computed } from 'vue';
import { Icon } from '@iconify/vue';
import Dropdown from '@/Components/Dropdown.vue';

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    align: {
        type: String,
        default: 'left',
    },
    width: {
        type: String,
        default: '48',
    },
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center px-1 py-1 border-b-2 border-primary dark:border-primaryDark text-sm font-medium leading-5 text-primaryDark dark:text-secondary focus:outline-none focus:border-primaryDark transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 py-1 border-b-2 border-transparent text-sm font-medium leading-5 text-primary dark:text-secondary hover:text-primaryDark dark:hover:text-secondaryDark hover:border-secondaryDark focus:outline-none focus:text-primaryDark dark:focus:text-secondaryDark focus:border-secondaryDark transition duration-150 ease-in-out';
});
</script>

<template>
    <div class="flex items-center -my-px">
        <Dropdown :align="align" :width="width">
            <template #trigger>
                <button
                    type="button"
                    :class="classes"
                >
                    {{ label }}
                    <Icon
                        icon="mdi:chevron-down"
                        class="ms-2 -me-0.5 size-4"
                    />
                </button>
            </template>

            <template #content>
                <slot />
            </template>
        </Dropdown>
    </div>
</template>
