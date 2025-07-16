<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';

const isAnswerOpen = ref(false);
</script>

<template>
    <div class="border border-gray-200 dark:border-gray-700 rounded-lg mb-4">
        <button
            @click="isAnswerOpen = !isAnswerOpen"
            class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
        >
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <slot name="question"></slot>
            </h3>
            <Icon
                :icon="isAnswerOpen ? 'mdi:expand-less' : 'mdi:expand-more'"
                class="text-xl text-gray-500 dark:text-gray-400 flex-shrink-0 ml-4"
            />
        </button>

        <div
            v-if="isAnswerOpen"
            class="px-4 pb-4 text-gray-700 dark:text-gray-300"
        >
            <slot name="answer"></slot>
        </div>
    </div>
</template>
