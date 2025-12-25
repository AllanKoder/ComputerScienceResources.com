<script setup>
import { Icon } from "@iconify/vue";
import { computed } from "vue";

const props = defineProps({
    modelValue: { type: Number, default: 0 },
    maxStars: { type: Number, default: 5 },
    size: { type: Number, default: 18 },
});

const formattedRating = computed(() => (props.modelValue || 0).toFixed(2));

const stars = computed(() => {
    const rating = props.modelValue || 0;
    const full = Math.floor(rating);
    const half = rating % 1 >= 0.5;
    const empty = props.maxStars - full - (half ? 1 : 0);
    return { full, hasHalf: half, empty };
});
const getStarClass = (type) =>
    ({
        full: "text-orange-500",
        half: "text-orange-500",
        empty: "text-gray-400 dark:text-gray-500",
    }[type]);
</script>

<template>
    <div class="inline-flex items-center flex-wrap gap-1">
        <div class="inline-flex items-center">
            <!-- Full stars -->
            <Icon
            v-for="i in stars.full"
            :key="'full-' + i"
            icon="mdi:star"
            :class="getStarClass('full')"
            :width="size"
            :height="size"
            />
            <!-- Half star -->
            <Icon
            v-if="stars.hasHalf"
            icon="mdi:star-half-full"
            :class="getStarClass('half')"
            :width="size"
            :height="size"
            />
            <!-- Empty stars -->
            <Icon
            v-for="i in stars.empty"
            :key="'empty-' + i"
            icon="mdi:star-outline"
            :class="getStarClass('empty')"
            :width="size"
            :height="size"
            />
        </div>
        {{ formattedRating }}
    </div>
</template>
