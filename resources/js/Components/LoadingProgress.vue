<template>
    <div>
      <!-- Progress Bar -->
      <div
        class="fixed top-0 left-0 right-0 z-[60]"
        :class="{ 'hidden': !loading }"
      >
        <div class="h-1 relative max-w-full overflow-hidden">
          <div class="w-full h-full bg-gray-200 absolute"></div>
          <div
            class="h-full bg-blue-600 absolute transition-all duration-500 ease-out"
            :style="{ width: `${progress}%` }"
          />
        </div>
      </div>

      <!-- Loading Overlay -->
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-show="loading" class="fixed inset-0 z-50">
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

          <!-- Loading Content -->
          <div class="flex items-center justify-center h-screen">
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full mx-4 relative">
              <div class="flex flex-col items-center space-y-4">
                <!-- Spinner -->
                <div class="text-blue-600 text-4xl animate-spin">
                  <i class="fas fa-circle-notch"></i>
                </div>

                <!-- Loading Text -->
                <div class="text-center">
                  <h3 class="text-lg font-medium text-gray-900">Loading</h3>
                  <p class="mt-1 text-sm text-gray-500">Please wait while we process your request...</p>
                </div>

                <!-- Progress Text -->
                <div class="text-sm text-gray-500">
                  {{ Math.round(progress) }}%
                </div>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </template>

  <script setup lang="ts">
  import { ref, onMounted, onUnmounted } from 'vue'
  import { router } from '@inertiajs/vue3'

  const progress = ref<number>(0)
  const loading = ref<boolean>(false)
  let timer: ReturnType<typeof setInterval> | null = null

  const startLoading = () => {
    loading.value = true
    progress.value = 0
    timer = setInterval(() => {
      if (progress.value < 90) {
        progress.value += Math.random() * 10
      }
    }, 200)
  }

  const finishLoading = () => {
    progress.value = 100
    setTimeout(() => {
      loading.value = false
      progress.value = 0
      if (timer) clearInterval(timer)
    }, 300)
  }

  onMounted(() => {
    router.on('before', startLoading)
    router.on('finish', finishLoading)
  })

  onUnmounted(() => {
    if (timer) clearInterval(timer)
  })
  </script>
