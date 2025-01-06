<script setup>
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
    ChartBarIcon,
    UserGroupIcon,
    DocumentTextIcon,
    ArrowPathIcon,
    DocumentArrowDownIcon,
    ArrowsRightLeftIcon
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    statistics: {
        type: Object,
        required: true
    },
    recentActivities: {
        type: Object,
        required: true
    },
    branches: {
        type: Array,
        required: true
    },
    filters: {
        type: Object,
        required: true
    },
    links: {
        type: Object,
        required: true
    }
})

const startDate = ref(props.filters.startDate)
const endDate = ref(props.filters.endDate)

const stats = [
    {
        name: 'Available Receipts',
        value: props.statistics.receipts.available,
        change: null,
        icon: DocumentTextIcon,
        color: 'text-green-600'
    },
    {
        name: 'Active Officers',
        value: props.statistics.activeOfficers,
        change: null,
        icon: UserGroupIcon,
        color: 'text-blue-600'
    },
    {
        name: 'Used Receipts',
        value: props.statistics.receipts.used,
        change: null,
        icon: DocumentArrowDownIcon,
        color: 'text-yellow-600'
    }
]

const updateDateRange = () => {
    router.get(route('admin.payment-receipt-dashboard'), {
        start_date: startDate.value,
        end_date: endDate.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

watch([startDate, endDate], ([newStartDate, newEndDate]) => {
    if (newStartDate && newEndDate) {
        updateDateRange()
    }
})
</script>

<template>
    <AdminLayout>
        <div class="min-h-full">

            <Head title="Payment Receipt Dashboard" />

            <!-- Main Content -->
            <main class="py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <!-- Header -->
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                                Payment Receipt Dashboard
                            </h1>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Manage and monitor receipt distribution across branches
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3">
                            <Link :href="links.receiptStocks"
                                class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:hover:bg-gray-700">
                            <ChartBarIcon class="mr-2 h-5 w-5" />
                            Stock Overview
                            </Link>
                            <Link :href="links.branchOfficers"
                                class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:hover:bg-gray-700">
                            <UserGroupIcon class="mr-2 h-5 w-5" />
                            Officers
                            </Link>
                            <Link :href="links.createDistribution"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                            <DocumentArrowDownIcon class="mr-2 h-5 w-5" />
                            New Distribution
                            </Link>
                        </div>
                    </div>

                    <!-- Date Filter -->
                    <div class="mt-6 flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <input v-model="startDate" type="date"
                                class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-800 dark:text-white dark:ring-gray-700 sm:text-sm sm:leading-6" />
                            <span class="text-gray-500">to</span>
                            <input v-model="endDate" type="date"
                                class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-800 dark:text-white dark:ring-gray-700 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="item in stats" :key="item.name"
                            class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6 dark:bg-gray-800">
                            <dt>
                                <div :class="[item.color, 'absolute rounded-md bg-white p-3 dark:bg-gray-700']">
                                    <component :is="item.icon" class="h-6 w-6" aria-hidden="true" />
                                </div>
                                <p class="ml-16 truncate text-sm font-medium text-gray-500 dark:text-gray-400">{{
                                    item.name }}</p>
                            </dt>
                            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ item.value }}</p>
                            </dd>
                        </div>
                    </div>

                    <!-- Branch Overview -->
                    <div class="mt-8">
                        <h2 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Branch Overview</h2>
                        <div class="mt-4 flow-root">
                            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                        <thead>
                                            <tr>
                                                <th scope="col"
                                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-0">
                                                    Branch</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                                    Available Receipts</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                                    Total Receipts</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            <tr v-for="branch in branches" :key="branch.id">
                                                <td
                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-0">
                                                    {{ branch.branch_name }}
                                                </td>
                                                <td
                                                    class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ branch.receipt_stock?.available_receipts ?? 0 }}
                                                </td>
                                                <td
                                                    class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ branch.receipt_stock?.total_receipts ?? 0 }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <!-- Recent Transfers -->
                        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <ArrowsRightLeftIcon class="h-5 w-5 text-gray-400" />
                                    <h2 class="ml-3 text-base font-semibold leading-6 text-gray-900 dark:text-white">
                                        Recent Transfers</h2>
                                </div>
                                <div class="mt-6 flow-root">
                                    <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                                        <li v-for="transfer in recentActivities.transfers" :key="transfer.id"
                                            class="py-5">
                                            <div class="relative">
                                                <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                                                    {{ transfer.from }} → {{ transfer.to }}
                                                </h3>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                    {{ transfer.quantity }} receipts transferred by {{ transfer.by }}
                                                </p>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{
                                                    transfer.date }}</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Distributions -->
                        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <DocumentArrowDownIcon class="h-5 w-5 text-gray-400" />
                                    <h2 class="ml-3 text-base font-semibold leading-6 text-gray-900 dark:text-white">
                                        Recent Distributions</h2>
                                </div>
                                <div class="mt-6 flow-root">
                                    <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                                        <li v-for="distribution in recentActivities.distributions"
                                            :key="distribution.id" class="py-5">
                                            <div class="relative">
                                                <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                                                    {{ distribution.branch }}
                                                </h3>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                    {{ distribution.quantity }} receipts distributed to {{
                                                    distribution.officer }}
                                                </p>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    by {{ distribution.by }} on {{ distribution.date }}
                                                </p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </AdminLayout>
</template>

<style scoped>
.dark input[type="date"] {
    color-scheme: dark;
}
</style>
