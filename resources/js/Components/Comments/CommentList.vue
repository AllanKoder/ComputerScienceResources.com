<script setup>
import { ref } from "vue";
import { Icon } from "@iconify/vue";
import SingleComment from "./SingleComment.vue";
import { getConfigData } from "@/Helpers/config";

const props = defineProps({
    idToChildren: {
        required: true,
        type: Object,
    },
    parentId: {
        default: null,
        type: Number,
    },
    depth: {
        default: 1,
        type: Number,
    },
});

const isCollapsed = ref(false);
const isNearCollapsing = ref(false);

const toggleCollapse = () => {
    isCollapsed.value = !isCollapsed.value;
};
</script>

<template>
    <div class="comments-list relative" :class="{'ml-4': parentId != null}">
        <!-- Collapse/Expand Control -->
        <div
            v-if="parentId != null"
            class="absolute left-0 top-0 bottom-0 flex items-center"
        >

        <div
            v-if="!isCollapsed"
            class="relative h-full cursor-pointer"
            @mouseenter="isNearCollapsing = true"
            @mouseleave="isNearCollapsing = false"
            @click="toggleCollapse"
        >
            <!-- Invisible clickable area -->
            <div
                class="absolute inset-y-0 left-1/2 transform -translate-x-1/2 w-8 bg-transparent"
            ></div>

            <!-- Visible line -->
            <div
                class="w-[1.2px] h-full bg-gray-300 transition-colors duration-200"
                :class="{'bg-primary': isNearCollapsing}"
            ></div>
        </div>

            <button
                v-else
                class="p-1 rounded-full border border-gray-300 bg-white hover:border-gray-500 hover:bg-gray-100 transition-colors duration-200"
                @click="toggleCollapse"
            >
                <Icon icon="mdi:chevron-right" width="20" height="20" class="text-gray-500 hover:text-blue-500" />
            </button>
        </div>

        <!-- Comments -->
        <div :class="{'pl-[1vw]': parentId != null}">
            <div v-if="!isCollapsed">
                <div v-for="comment in idToChildren[parentId] || []" :key="comment.id">
                    <SingleComment
                        :comment="comment"
                        :depth="depth + 1"
                    />

                    <CommentList
                        v-if="depth < getConfigData().COMMENT_MAX_DEPTH && comment"
                        :depth="depth + 1"
                        :parent-id="comment.id"
                        :id-to-children="idToChildren"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
