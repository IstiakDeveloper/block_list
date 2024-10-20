<template>
    <div v-if="shouldRender" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
      <div class="flex flex-1 justify-between sm:hidden">
        <button
          @click="$emit('paginate', links.prev)"
          :disabled="!links.prev"
          :class="[
            !links.prev ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
            'relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700'
          ]"
        >
          Previous
        </button>
        <button
          @click="$emit('paginate', links.next)"
          :disabled="!links.next"
          :class="[
            !links.next ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50',
            'relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700'
          ]"
        >
          Next
        </button>
      </div>
      <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Showing
            <span class="font-medium">{{ meta.from || 0 }}</span>
            to
            <span class="font-medium">{{ meta.to || 0 }}</span>
            of
            <span class="font-medium">{{ meta.total || 0 }}</span>
            results
          </p>
        </div>
        <div v-if="links && Object.keys(links).length > 3">
          <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
            <button
              v-for="(link, index) in links"
              :key="index"
              @click="$emit('paginate', link.url)"
              :disabled="!link.url"
              :class="[
                link.active ? 'z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0',
                !link.url ? 'cursor-not-allowed opacity-50' : '',
                index === 0 ? 'rounded-l-md' : '',
                index === Object.keys(links).length - 1 ? 'rounded-r-md' : '',
                'relative inline-flex items-center px-4 py-2 text-sm font-semibold'
              ]"
              v-html="link.label"
            ></button>
          </nav>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { defineProps, defineEmits, computed } from 'vue';

  const props = defineProps({
    links: {
      type: Object,
      default: () => ({})
    },
    meta: {
      type: Object,
      default: () => ({})
    }
  });

  const shouldRender = computed(() => {
    return props.meta && props.links &&
           (props.meta.total > props.meta.per_page || props.meta.current_page > 1);
  });

  defineEmits(['paginate']);
  </script>
