<template>
    <div class="flex items-center justify-center">
        <div class="relative w-fit">
            <!-- Cat with glasses animation container -->
            <div class="cat-loader relative w-fit flex items-center justify-center">
                <img
                    src="/images/cat_head.png"
                    class="w-48 h-32 relative z-10 rounded-full"
                />
                <!-- Glowing background effect -->
                <div class="absolute inset-1 rounded-full blur-lg glow-effect"></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.cat-loader {
    animation: bounce 2s infinite;
}

.glow-effect {
    animation: glow 2s infinite;
    background-size: 300% 300%;
    background-image: linear-gradient(
        45deg,
        theme('colors.secondary') 0%,
        theme('colors.accent') 50%,
        theme('colors.secondary') 100%
    );
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(2px);
    }
    50% {
        transform: translateY(-2px);
    }
}

@keyframes glow {
    0%, 100% {
        opacity: 0.6;
        background-position: 0% 50%;
    }
    50% {
        opacity: 0.9;
        background-position: 100% 50%;
    }
}
</style>
