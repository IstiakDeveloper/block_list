<template>
    <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 p-4 sm:p-6">
        <!-- Info Text -->
        <span class="text-sm text-gray-700 dark:text-gray-300">
            Showing {{ data.from }} to {{ data.to }} of {{ data.total }} {{ itemName }}
        </span>

        <!-- Pagination Controls -->
        <div class="flex items-center space-x-2">
            <!-- First Page -->
            <button @click="goToPage(1)"
                    :disabled="data.current_page === 1"
                    class="btn-secondary w-10 h-10 flex items-center justify-center"
                    :class="{ 'opacity-50 cursor-not-allowed': data.current_page === 1 }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>

            <!-- Previous -->
            <button @click="goToPage(data.current_page - 1)"
                    :disabled="!data.prev_page_url"
                    class="btn-secondary w-10 h-10 flex items-center justify-center"
                    :class="{ 'opacity-50 cursor-not-allowed': !data.prev_page_url }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Page Numbers -->
            <div class="hidden sm:flex space-x-1">
                <template v-for="pageNumber in displayedPages" :key="pageNumber">
                    <button v-if="pageNumber !== '...'"
                        @click="goToPage(pageNumber)"
                        class="w-10 h-10 flex items-center justify-center rounded"
                        :class="[
                            pageNumber === data.current_page
                                ? 'bg-blue-600 text-white font-semibold'
                                : 'btn-secondary hover:bg-gray-200 dark:hover:bg-gray-700'
                        ]">
                        {{ pageNumber }}
                    </button>
                    <span v-else
                        class="w-10 h-10 flex items-center justify-center text-gray-500">
                        ...
                    </span>
                </template>
            </div>

            <!-- Next -->
            <button @click="goToPage(data.current_page + 1)"
                    :disabled="!data.next_page_url"
                    class="btn-secondary w-10 h-10 flex items-center justify-center"
                    :class="{ 'opacity-50 cursor-not-allowed': !data.next_page_url }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Last Page -->
            <button @click="goToPage(data.last_page)"
                    :disabled="data.current_page === data.last_page"
                    class="btn-secondary w-10 h-10 flex items-center justify-center"
                    :class="{ 'opacity-50 cursor-not-allowed': data.current_page === data.last_page }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
    itemName: {
        type: String,
        default: 'items'
    },
    preserveScroll: {
        type: Boolean,
        default: true
    },
    preserveState: {
        type: Boolean,
        default: true
    },
    extraParams: {
        type: Object,
        default: () => ({})
    }
});

const displayedPages = computed(() => {
    const current = props.data.current_page;
    const last = props.data.last_page;
    const delta = 2;
    const pages = [];

    pages.push(1);

    const rangeStart = Math.max(2, current - delta);
    const rangeEnd = Math.min(last - 1, current + delta);

    if (rangeStart > 2) pages.push('...');

    for (let i = rangeStart; i <= rangeEnd; i++) {
        pages.push(i);
    }

    if (rangeEnd < last - 1) pages.push('...');
    if (last > 1) pages.push(last);

    return pages;
});

function goToPage(page) {
    const params = new URLSearchParams(window.location.search);
    params.set('page', page);

    // Add any extra parameters
    Object.entries(props.extraParams).forEach(([key, value]) => {
        if (value) params.set(key, value);
    });

    router.get(window.location.pathname + '?' + params.toString(), {}, {
        preserveState: props.preserveState,
        preserveScroll: props.preserveScroll
    });
}
</script>

<style scoped>
.btn-secondary {
    @apply bg-gray-100 text-gray-700 font-medium rounded transition-colors duration-150
    hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600
    disabled:opacity-50 disabled:cursor-not-allowed;
}
</style>
