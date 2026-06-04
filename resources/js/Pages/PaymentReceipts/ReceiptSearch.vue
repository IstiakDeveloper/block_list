<template>
    <AdminLayout title="Search Receipt">
        <div class="py-6 min-h-screen bg-slate-50/50 dark:bg-slate-950/20 text-slate-800 dark:text-slate-100">
            <div class="mx-auto space-y-6 max-w-4xl px-4 sm:px-6 lg:px-8">
                
                <!-- Header -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Search Receipt
                        </h1>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            Track the location, allocation history, and current status of receipt books.
                        </p>
                    </div>
                    <Link
                        :href="route('payment-receipts.index')"
                        class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200 shrink-0"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Back to Inventory
                    </Link>
                </div>

                <!-- Premium Search Control Center -->
                <div class="p-6 bg-white dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/80 rounded-2xl shadow-sm space-y-6">
                    <!-- Pill Toggle for Search Mode -->
                    <div class="flex justify-center">
                        <div class="inline-flex p-1 bg-slate-100/80 dark:bg-slate-950/80 border border-slate-200/30 dark:border-slate-800/50 rounded-2xl">
                            <button type="button" @click="form.search_type = 'book'"
                                class="px-5 py-2 text-xs font-bold rounded-xl transition-all duration-200"
                                :class="form.search_type === 'book' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'">
                                Book Number
                            </button>
                            <button type="button" @click="form.search_type = 'receipt'"
                                class="px-5 py-2 text-xs font-bold rounded-xl transition-all duration-200"
                                :class="form.search_type === 'receipt' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'">
                                Receipt Number
                            </button>
                        </div>
                    </div>

                    <!-- Search Input Wrapper -->
                    <form @submit.prevent="submitSearch" class="max-w-2xl mx-auto">
                        <div class="relative flex items-center">
                            <Search class="absolute w-5 h-5 text-slate-400 left-4" />
                            <input
                                id="search-query"
                                v-model="form.query"
                                type="text"
                                inputmode="numeric"
                                :placeholder="form.search_type === 'book' ? 'Enter Book Number (e.g. 665)' : 'Enter Receipt Number (e.g. 66501)'"
                                class="w-full pl-12 pr-28 py-3.5 border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 rounded-2xl dark:text-slate-100 focus:border-blue-500 focus:ring-blue-500/20 text-sm font-semibold"
                                autofocus
                            />
                            <button
                                type="submit"
                                class="absolute right-2 px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.97] rounded-xl transition-all duration-200 shadow-sm shadow-blue-500/15"
                            >
                                Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Error Message -->
                <div
                    v-if="error"
                    class="flex items-start gap-3 p-4 text-amber-800 bg-amber-50 border border-amber-200 rounded-xl dark:bg-amber-950/30 dark:border-amber-800/80 dark:text-amber-200 animate-fade-in"
                >
                    <AlertCircle class="w-5 h-5 shrink-0 text-amber-600 dark:text-amber-400" />
                    <p class="text-xs font-semibold leading-relaxed">{{ error }}</p>
                </div>

                <!-- Search Results -->
                <div v-if="results.length" class="space-y-6">
                    <p v-if="results.length > 1" class="text-xs text-slate-400 font-bold dark:text-slate-500 uppercase tracking-wider">
                        {{ results.length }} matches found (Book numbers may overlap across different lots).
                    </p>

                    <article
                        v-for="result in results"
                        :key="result.book_id"
                        class="overflow-hidden bg-white dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/80 rounded-3xl shadow-sm space-y-6 p-6"
                    >
                        <!-- Top Summary Header -->
                        <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-slate-100 dark:border-slate-800/40">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-lg font-black text-slate-900 dark:text-white">
                                        Book #{{ result.book_number }}
                                    </h2>
                                    <span
                                        class="inline-flex px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md border"
                                        :class="statusBadgeClass(result)"
                                    >
                                        {{ result.status_label }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400 dark:text-slate-500 font-semibold">
                                    <span>Receipts: <strong>{{ formatNumber(result.receipt_from) }} – {{ formatNumber(result.receipt_to) }}</strong></span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-800"></span>
                                    <span>Lot: <strong>{{ result.lot.lot_number }}</strong> <span v-if="result.lot.lot_name">({{ result.lot.lot_name }})</span></span>
                                    <span v-if="result.searched_receipt_number" class="text-blue-500 dark:text-blue-400">
                                        (Searched: #{{ formatNumber(result.searched_receipt_number) }})
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 3-Step Lifecycle Pipeline (Flow Visualization) -->
                        <div class="space-y-3">
                            <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                Distribution Pipeline
                            </h3>
                            <div class="relative flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 p-5 bg-slate-50 dark:bg-slate-950/20 rounded-2xl border border-slate-100 dark:border-slate-800/50">
                                
                                <!-- Step 1: Head Office Entry -->
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs bg-indigo-500 text-white shrink-0 shadow-sm shadow-indigo-500/10">
                                        1
                                    </div>
                                    <div>
                                        <div class="font-bold text-[9px] uppercase tracking-wider text-indigo-500">Step 1</div>
                                        <div class="text-xs font-black text-slate-800 dark:text-slate-200">Head Office Stock</div>
                                        <div class="text-[10px] text-slate-400 font-medium mt-0.5">Lot {{ result.lot.lot_number }} Received</div>
                                    </div>
                                </div>

                                <!-- Connection Arrow -->
                                <div class="hidden md:flex text-slate-300 dark:text-slate-800 shrink-0">
                                    <ArrowRight class="w-4 h-4" />
                                </div>

                                <!-- Step 2: Distributed to Branch -->
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0"
                                        :class="result.distribution_summary.distributed_to_branch ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-500/10' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600'">
                                        2
                                    </div>
                                    <div>
                                        <div class="font-bold text-[9px] uppercase tracking-wider"
                                            :class="result.distribution_summary.distributed_to_branch ? 'text-emerald-500' : 'text-slate-400 dark:text-slate-600'">
                                            Step 2
                                        </div>
                                        <div class="text-xs font-black"
                                            :class="result.distribution_summary.distributed_to_branch ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400 dark:text-slate-600'">
                                            Branch Distribution
                                        </div>
                                        <div v-if="result.distribution_summary.distributed_to_branch" class="text-[10px] text-slate-500 font-medium mt-0.5">
                                            {{ result.distribution_summary.branch_name }}
                                        </div>
                                        <div v-else class="text-[10px] text-slate-400 dark:text-slate-600 font-medium mt-0.5">Not Distributed</div>
                                    </div>
                                </div>

                                <!-- Connection Arrow -->
                                <div class="hidden md:flex text-slate-300 dark:text-slate-800 shrink-0">
                                    <ArrowRight class="w-4 h-4" />
                                </div>

                                <!-- Step 3: Allocated to Person -->
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0"
                                        :class="result.distribution_summary.distributed_to_person ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/10' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600'">
                                        3
                                    </div>
                                    <div>
                                        <div class="font-bold text-[9px] uppercase tracking-wider"
                                            :class="result.distribution_summary.distributed_to_person ? 'text-blue-500' : 'text-slate-400 dark:text-slate-600'">
                                            Step 3
                                        </div>
                                        <div class="text-xs font-black"
                                            :class="result.distribution_summary.distributed_to_person ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400 dark:text-slate-600'">
                                            Person Allocation
                                        </div>
                                        <div v-if="result.distribution_summary.distributed_to_person" class="text-[10px] text-slate-500 font-medium mt-0.5">
                                            {{ result.distribution_summary.given_to }} <span class="text-[9px] text-slate-400" v-if="result.distribution_summary.pin_number">(PIN: {{ result.distribution_summary.pin_number }})</span>
                                        </div>
                                        <div v-else class="text-[10px] text-slate-400 dark:text-slate-600 font-medium mt-0.5">Not Allocated</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Current Location Highlight Card -->
                        <div class="space-y-2">
                            <div class="p-4 rounded-2xl flex items-start gap-3 border"
                                :class="locationCardClass(result.current_location.type)">
                                <div class="p-2 bg-white dark:bg-slate-900 rounded-xl shrink-0 border border-slate-100 dark:border-slate-800">
                                    <MapPin class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="space-y-1">
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Current Status & Location</div>
                                    <div class="text-sm font-black">{{ result.current_location.label }}</div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-semibold">
                                        {{ result.current_location.description }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Distribution Details Blocks -->
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Branch distribution summary -->
                            <div class="p-4.5 rounded-2xl bg-white dark:bg-slate-950/20 border border-slate-200/50 dark:border-slate-800/80 space-y-2">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                    Branch Distribution Info
                                </div>
                                <div v-if="result.distribution_summary.distributed_to_branch" class="space-y-1 text-xs">
                                    <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-extrabold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Distributed Successfully
                                    </div>
                                    <p class="text-slate-600 dark:text-slate-400 font-semibold">
                                        Branch: <strong class="text-slate-800 dark:text-slate-200">{{ result.distribution_summary.branch_name }}</strong>
                                    </p>
                                    <p v-if="result.distribution_summary.branch_distributed_date" class="text-[10px] text-slate-400">
                                        Date: {{ result.distribution_summary.branch_distributed_date }}
                                    </p>
                                </div>
                                <div v-else class="text-xs text-slate-400 dark:text-slate-600 font-bold py-1">
                                    No branch distribution recorded
                                </div>
                            </div>

                            <!-- Person distribution summary -->
                            <div class="p-4.5 rounded-2xl bg-white dark:bg-slate-950/20 border border-slate-200/50 dark:border-slate-800/80 space-y-2">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                    Person Distribution Info
                                </div>
                                <div v-if="result.distribution_summary.distributed_to_person" class="space-y-1 text-xs">
                                    <div class="flex items-center gap-1.5 text-blue-600 dark:text-blue-400 font-extrabold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Distributed Successfully
                                    </div>
                                    <p class="text-slate-600 dark:text-slate-400 font-semibold">
                                        Given to: <strong class="text-slate-800 dark:text-slate-200">{{ result.distribution_summary.given_to }}</strong>
                                    </p>
                                    <p v-if="result.distribution_summary.pin_number" class="text-slate-600 dark:text-slate-400 font-semibold">
                                        PIN: <strong class="text-slate-800 dark:text-slate-200">{{ result.distribution_summary.pin_number }}</strong>
                                    </p>
                                    <p v-if="result.distribution_summary.person_distributed_date" class="text-[10px] text-slate-400">
                                        Date: {{ result.distribution_summary.person_distributed_date }}
                                    </p>
                                </div>
                                <div v-else class="text-xs text-slate-400 dark:text-slate-600 font-bold py-1">
                                    No person allocation recorded
                                </div>
                            </div>
                        </div>

                        <!-- Full Transaction Log Table -->
                        <div v-if="result.history.length" class="space-y-3">
                            <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                Book Audit Log
                            </h3>
                            <div class="bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl overflow-hidden shadow-sm">
                                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                                    <thead class="bg-slate-50/75 dark:bg-slate-900/40">
                                        <tr>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                                Date
                                            </th>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                                Action Type
                                            </th>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                                Details
                                            </th>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                                Receipt Range
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900/20">
                                        <tr v-for="tx in result.history" :key="tx.id"
                                            class="transition-colors hover:bg-slate-50/40 dark:hover:bg-slate-800/20">
                                            <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap dark:text-slate-400">
                                                {{ tx.transaction_date }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full border"
                                                    :class="txTypeClass(tx.transaction_type)">
                                                    {{ tx.transaction_type_label }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-700 dark:text-slate-300 font-semibold">
                                                <template v-if="tx.transaction_type === 'distribute_to_person'">
                                                    {{ tx.given_to }}
                                                    <span v-if="tx.pin_number" class="text-slate-500 font-bold ml-1">(PIN: {{ tx.pin_number }})</span>
                                                    <span v-if="tx.branch_name" class="block text-[10px] text-slate-400 font-medium mt-0.5">Branch: {{ tx.branch_name }}</span>
                                                </template>
                                                <template v-else-if="tx.transaction_type === 'distribute_to_branch'">
                                                    {{ tx.branch_name }}
                                                    <span v-if="tx.received_by" class="block text-[10px] text-slate-400 font-medium mt-0.5">Received by: {{ tx.received_by }}</span>
                                                </template>
                                                <template v-else>
                                                    Head Office Stock In
                                                    <span v-if="tx.received_by" class="block text-[10px] text-slate-400 font-medium mt-0.5">By: {{ tx.received_by }}</span>
                                                </template>
                                                <span v-if="tx.remarks" class="block text-[10px] text-slate-400/80 italic font-medium mt-1">{{ tx.remarks }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-xs font-bold text-slate-800 whitespace-nowrap dark:text-slate-200">
                                                {{ formatNumber(tx.receipt_from) }} – {{ formatNumber(tx.receipt_to) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Empty state -->
                <div
                    v-else-if="!error && !filters.query"
                    class="text-center py-16 bg-white dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/80 rounded-3xl"
                >
                    <Search class="w-10 h-10 mx-auto mb-3 opacity-30 text-slate-400" />
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300">Start Tracking</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto leading-relaxed">
                        Enter a valid book number or individual receipt number above to visualize its timeline and current assignment.
                    </p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Search, ArrowLeft, MapPin, AlertCircle, ArrowRight } from 'lucide-vue-next';

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({ search_type: 'book', query: '' }),
    },
    results: {
        type: Array,
        default: () => [],
    },
    error: {
        type: String,
        default: null,
    },
});

const form = reactive({
    search_type: props.filters.search_type || 'book',
    query: props.filters.query || '',
});

const submitSearch = () => {
    router.get(route('payment-receipts.search'), {
        search_type: form.search_type,
        query: form.query.trim(),
    }, {
        preserveState: true,
        replace: true,
    });
};

const formatNumber = (number) => {
    if (number === 0) return '0';
    if (!number) return '-';
    return number.toLocaleString('en-US', { maximumFractionDigits: 0 });
};

const statusBadgeClass = (result) => {
    const type = result.current_location?.type;
    if (type === 'head_office') return 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800';
    if (type === 'branch') return 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30';
    return 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30';
};

const locationCardClass = (type) => {
    if (type === 'head_office') return 'bg-slate-50/50 border-slate-200 dark:bg-slate-900/30 dark:border-slate-800';
    if (type === 'branch') return 'bg-emerald-50/20 border-emerald-200/50 dark:bg-emerald-950/10 dark:border-emerald-800/40';
    return 'bg-blue-50/20 border-blue-200/50 dark:bg-blue-950/10 dark:border-blue-800/40';
};

const txTypeClass = (type) => {
    if (type === 'stock_in') return 'bg-purple-50 text-purple-700 border-purple-100 dark:bg-purple-950/20 dark:text-purple-400 dark:border-purple-900/30';
    if (type === 'distribute_to_branch') return 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30';
    if (type === 'distribute_to_person') return 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30';
    return 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-800';
};
</script>

<style scoped>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
