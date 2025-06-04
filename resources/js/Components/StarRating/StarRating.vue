<script setup>
import { Icon } from "@iconify/vue";
import { computed } from "vue";

const props = defineProps({
    modelValue: {
        type: Number,
        default: 0
    },
    maxStars: {
        type: Number,
        default: 5
    },
    size: {
        type: Number,
        default: 18
    }
});

const stars = computed(() => {
    const rating = props.modelValue || 0;
    const fullStars = Math.floor(rating);
    const hasHalf = rating % 1 >= 0.5;
    const emptyStars = props.maxStars - Math.ceil(rating);

    return {
        full: fullStars,
        hasHalf,
        empty: emptyStars
    };
});

const getStarClass = (type) => {
    const colorClass = {
        full: 'text-orange-500',
        half: 'text-orange-500',
        empty: 'text-gray-400'
    };

    return colorClass[type];
};
</script>

<template>
    <div class="inline-flex items-center">
        <!-- Full stars -->
        <Icon
            v-for="i in stars.full"
            :key="'full-' + i"
            icon="material-symbols:star-rounded"
            :class="getStarClass('full')"
            :width="size"
            :height="size"
        />

        <!-- Half star -->
        <Icon
            v-if="stars.hasHalf"
            icon="material-symbols:star-half-rounded"
            :class="getStarClass('half')"
            :width="size"
            :height="size"
        />

        <!-- Empty stars -->
        <Icon
            v-for="i in stars.empty"
            :key="'empty-' + i"
            icon="material-symbols:star-outline-rounded"
            :class="getStarClass('empty')"
            :width="size"
            :height="size"
        />
    </div>
</template>
