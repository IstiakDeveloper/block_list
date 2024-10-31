<template>
    <Transition
        enter-active-class="transition-all duration-500 ease-out"
        enter-from-class="opacity-0 translate-y-10"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-500 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-10"
    >
        <button
            v-show="isVisible"
            @click="scrollToTop"
            class="group fixed bottom-8 right-8 p-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-full shadow-lg hover:shadow-2xl transition-all duration-300 ease-in-out transform hover:scale-110 z-[60] backdrop-blur-sm bg-opacity-90"
            aria-label="Scroll to top"
        >
            <div class="relative">
                <svg
                    class="w-6 h-6 transform transition-transform duration-300 group-hover:-translate-y-1"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 15l7-7 7 7"
                    />
                </svg>
                <div class="absolute inset-0 rounded-full animate-ping opacity-25 bg-white"></div>
            </div>
            <span class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Back to top
            </span>
        </button>
    </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isVisible = ref(false);

const checkScroll = () => {
    const mainContent = document.querySelector('main');
    isVisible.value = mainContent?.scrollTop > 10;
};

const scrollToTop = () => {
    const mainContent = document.querySelector('main');
    mainContent?.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

onMounted(() => {
    const mainContent = document.querySelector('main');
    mainContent?.addEventListener('scroll', checkScroll);
});

onUnmounted(() => {
    const mainContent = document.querySelector('main');
    mainContent?.removeEventListener('scroll', checkScroll);
});
</script>
