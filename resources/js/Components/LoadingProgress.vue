<template>
    <div>
      <!-- Progress Bar -->
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-show="loading || progress > 0"
          class="fixed top-0 right-0 z-[60] p-4 flex flex-col items-end gap-2"
        >
          <!-- Progress Indicator -->
          <div class="bg-white rounded-full shadow-md border border-gray-200 px-3 py-2 flex items-center gap-2">
            <div class="w-4 h-4 rounded-full border-2 border-blue-600 border-t-transparent animate-spin"></div>
            <span class="text-sm font-medium text-gray-700">{{ Math.round(progress) }}%</span>
          </div>

          <!-- Progress Bar -->
          <div class="w-[200px] h-1 bg-white rounded-full shadow-sm overflow-hidden">
            <div
              class="h-full bg-blue-600 transition-all duration-500 ease-out"
              :style="{ width: `${Math.min(progress, 100)}%` }"
            />
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
  let progressTimer: ReturnType<typeof setInterval> | null = null
  let completeTimer: ReturnType<typeof setTimeout> | null = null

  const startLoading = () => {
    // Clear any existing timers
    if (progressTimer) clearInterval(progressTimer)
    if (completeTimer) clearTimeout(completeTimer)

    loading.value = true
    progress.value = 0

    progressTimer = setInterval(() => {
      if (progress.value < 90) {
        // Smoother progress increment
        progress.value += Math.random() * 3
      }
    }, 100)
  }

  const finishLoading = () => {
    if (progressTimer) {
      clearInterval(progressTimer)
      progressTimer = null
    }

    progress.value = 100

    completeTimer = setTimeout(() => {
      loading.value = false
      progress.value = 0
    }, 400)
  }

  onMounted(() => {
    try {
      router.on('before', () => {
        startLoading()
      })

      router.on('finish', () => {
        finishLoading()
      })

      // Handle errors
      router.on('error', () => {
        finishLoading()
      })
    } catch (error) {
      console.error('Error setting up router events:', error)
    }
  })

  onUnmounted(() => {
    // Clean up all timers
    if (progressTimer) clearInterval(progressTimer)
    if (completeTimer) clearTimeout(completeTimer)
  })
  </script>
