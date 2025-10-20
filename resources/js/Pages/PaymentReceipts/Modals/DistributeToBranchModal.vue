<template>
    <Modal :show="show" @close="handleClose" maxWidth="2xl">
        <div class="p-4 bg-white rounded-lg dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Distribute Stock to Branch
                </h2>
                <button @click="handleClose" type="button"
                    class="p-2 text-gray-400 transition-colors rounded-lg hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Error Display -->
            <div v-if="form.errors && Object.keys(form.errors).length > 0"
                class="flex items-start gap-2 p-4 mb-4 border border-red-400 rounded-lg bg-red-50 dark:bg-red-900/30 dark:border-red-500">
                <AlertCircle class="w-5 h-5 mt-0.5 text-red-600 dark:text-red-400 flex-shrink-0" />
                <div class="space-y-1">
                    <div v-for="(error, key) in form.errors" :key="key" class="text-sm text-red-600 dark:text-red-400">
                        {{ error }}
                    </div>
                </div>
            </div>

            <form @submit.prevent="submitForm">
                <!-- Branch Selection with Search -->
                <div class="mb-6">
                    <Label value="Select Branch" required class="text-gray-700 dark:text-gray-300" />
                    <div class="relative" v-click-outside="() => showBranchDropdown = false">
                        <input
                            type="text"
                            v-model="branchSearch"
                            placeholder="Search by branch code or name..."
                            class="block w-full mt-1 pl-8 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            @focus="showBranchDropdown = true"
                        />
                        <Search class="absolute w-4 h-4 text-gray-400 left-2.5 top-3.5" />
                        <div v-if="showBranchDropdown && filteredBranches.length > 0"
                            class="absolute z-50 w-full mt-1 overflow-auto bg-white border border-gray-300 rounded-md shadow-lg max-h-60 dark:bg-gray-700 dark:border-gray-600">
                            <div v-for="branch in filteredBranches"
                                :key="branch.id"
                                @click="selectBranch(branch)"
                                class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ branch.branch_code }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ branch.branch_name }}</div>
                            </div>
                        </div>
                    </div>
                    <div v-if="selectedBranch" class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                        <div class="text-sm text-blue-700 dark:text-blue-300">
                            Selected: {{ selectedBranch.branch_code }} - {{ selectedBranch.branch_name }}
                        </div>
                    </div>
                </div>

                <!-- Auto Selected Lot -->
                <div class="mb-6">
                    <Label value="Active Lot" required class="text-gray-700 dark:text-gray-300" />
                    <div v-if="availableLot" class="mt-1 p-3 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-700">
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                            <div>
                                <div class="font-medium text-blue-900 dark:text-blue-100">
                                    {{ availableLot.lot_number }}
                                    {{ availableLot.lot_name ? ` - ${availableLot.lot_name}` : '' }}
                                </div>
                                <div class="text-sm text-blue-700 dark:text-blue-300">
                                    {{ availableLot.available_books_count || 0 }} books available
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="mt-1 p-3 border border-yellow-200 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-700">
                        <div class="flex items-center gap-2">
                            <AlertTriangle class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                            <span class="text-sm text-yellow-700 dark:text-yellow-300">
                                No active lot with available books
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loadingBooks" class="flex items-center gap-2 p-4 mb-6 rounded-lg bg-blue-50 dark:bg-blue-900/30">
                    <Loader2 class="w-5 h-5 text-blue-600 animate-spin dark:text-blue-400" />
                    <span class="text-sm text-blue-700 dark:text-blue-300">Loading available books...</span>
                </div>

                <!-- Summary Info Box -->
                <div v-if="form.lot_id && availableBooks.length > 0"
                    class="p-3 mb-4 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-700">
                    <div class="flex items-center gap-2 mb-2">
                        <Info class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                            Stock Information
                        </span>
                    </div>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        This lot has <strong>{{ availableBooks.length }} books</strong> available at Head Office.
                        You can distribute books from <strong>{{ minBookNumber }}</strong> to <strong>{{ maxBookNumber }}</strong>.
                    </p>
                </div>

                <!-- No Books Warning -->
                <div v-else-if="form.lot_id && !loadingBooks" class="flex items-center gap-2 p-4 mb-6 border border-yellow-200 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-700">
                    <AlertTriangle class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                    <span class="text-sm text-yellow-700 dark:text-yellow-300">No books available in this lot</span>
                </div>

                <!-- Book Number Range Selection -->
                <div class="p-4 mb-6 border border-gray-200 rounded-lg dark:border-gray-600">
                    <h3 class="flex items-center gap-2 mb-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                        <Package class="w-4 h-4" />
                        Select Book Range to Distribute
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <Label value="From Book Number" required class="text-gray-700 dark:text-gray-300" />
                            <Input v-model.number="form.book_from" type="number"
                                :min="minBookNumber" :max="maxBookNumber"
                                required placeholder="e.g., 665"
                                :disabled="!form.lot_id || availableBooks.length === 0"
                                class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed" />
                        </div>
                        <div>
                            <Label value="To Book Number" required class="text-gray-700 dark:text-gray-300" />
                            <Input v-model.number="form.book_to" type="number"
                                :min="form.book_from || minBookNumber" :max="maxBookNumber"
                                required placeholder="e.g., 670"
                                :disabled="!form.lot_id || availableBooks.length === 0"
                                class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed" />
                        </div>
                    </div>
                </div>

                <!-- Received By (Required) -->
                <div class="mb-6">
                    <Label value="Received By" required class="text-gray-700 dark:text-gray-300" />
                    <Input v-model="form.received_by" type="text" required placeholder="Name of person receiving"
                        class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                </div>

                <!-- Auto Calculate Distribution Summary -->
                <div v-if="form.book_from && form.book_to && form.book_to >= form.book_from"
                    class="p-4 mb-6 border-2 border-green-200 rounded-lg bg-green-50 dark:bg-green-900/20 dark:border-green-700">
                    <div class="flex items-center gap-2 mb-3">
                        <Calculator class="w-5 h-5 text-green-600 dark:text-green-400" />
                        <h4 class="text-sm font-semibold text-green-900 dark:text-green-100">Distribution Summary:</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg dark:bg-gray-800">
                            <span class="text-sm text-green-700 dark:text-green-300">Total Books:</span>
                            <span class="text-lg font-bold text-green-900 dark:text-green-100">{{ totalBooks }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg dark:bg-gray-800">
                            <span class="text-sm text-green-700 dark:text-green-300">Total Receipts:</span>
                            <span class="text-lg font-bold text-green-900 dark:text-green-100">{{ totalReceipts }}</span>
                        </div>
                        <div class="col-span-2 p-3 bg-white rounded-lg dark:bg-gray-800">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-green-700 dark:text-green-300">Receipt Range:</span>
                                <span class="text-base font-bold text-green-900 dark:text-green-100">
                                    {{ receiptFrom }} - {{ receiptTo }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="handleClose" type="button" :disabled="form.processing">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="!isFormValid || form.processing">
                        <Loader2 v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                        <SendIcon v-else class="w-4 h-4 mr-2" />
                        {{ form.processing ? 'Distributing...' : 'Distribute to Branch' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {
    Loader2, X, AlertCircle, Package, Calculator, SendIcon,
    CheckCircle, AlertTriangle, Search
} from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    branches: {
        type: Array,
        default: () => []
    },
    activeLots: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'success']);

const availableBooks = ref([]);
const loadingBooks = ref(false);
const branchSearch = ref('');
const showBranchDropdown = ref(false);
const selectedBranch = ref(null);

// Sort branches by branch code
const sortedBranches = computed(() => {
    return [...props.branches].sort((a, b) => a.branch_code.localeCompare(b.branch_code));
});

// Filter branches based on search
const filteredBranches = computed(() => {
    const search = branchSearch.value.toLowerCase();
    return sortedBranches.value.filter(branch =>
        branch.branch_code.toLowerCase().includes(search) ||
        branch.branch_name.toLowerCase().includes(search)
    );
});

// Get available lot with books
const availableLot = computed(() => {
    return props.activeLots.find(lot => lot.available_books_count > 0);
});

const form = useForm({
    branch_id: '',
    lot_id: '',
    book_from: null,
    book_to: null,
    received_by: ''
});

// Select branch and auto-load lot
const selectBranch = (branch) => {
    selectedBranch.value = branch;
    form.branch_id = branch.id;
    showBranchDropdown.value = false;
    branchSearch.value = branch.branch_code;

    // Auto select the first available lot
    if (availableLot.value) {
        form.lot_id = availableLot.value.id;
        loadLotBooks();
    }
};

// Watch for lot changes and component mounting
watch(() => props.show, (newVal) => {
    if (newVal) {
        // When modal opens, auto-select the first available lot
        if (availableLot.value) {
            form.lot_id = availableLot.value.id;
            loadLotBooks();
        }
    }
});

// Watch for lot selection (this will run when lot changes)
watch(() => form.lot_id, () => {
    availableBooks.value = [];
    form.book_from = null;
    form.book_to = null;
});

const minBookNumber = computed(() => {
    if (availableBooks.value.length === 0) return 1;
    return availableBooks.value[0].book_number;
});

const maxBookNumber = computed(() => {
    if (availableBooks.value.length === 0) return 1;
    return availableBooks.value[availableBooks.value.length - 1].book_number;
});

const totalBooks = computed(() => {
    if (!form.book_from || !form.book_to || form.book_to < form.book_from) return 0;
    return form.book_to - form.book_from + 1;
});

const totalReceipts = computed(() => totalBooks.value * 100);

const receiptFrom = computed(() => {
    if (!form.book_from) return 0;
    return ((form.book_from - 1) * 100) + 1;
});

const receiptTo = computed(() => {
    if (!form.book_to) return 0;
    return form.book_to * 100;
});

const isFormValid = computed(() => {
    if (!form.branch_id || !form.lot_id) return false;
    if (!form.book_from || !form.book_to) return false;
    if (form.book_to < form.book_from) return false;
    if (availableBooks.value.length === 0) return false;
    if (!form.received_by) return false;
    return true;
});

const loadLotBooks = async () => {
    if (!form.lot_id) {
        availableBooks.value = [];
        return;
    }

    loadingBooks.value = true;

    try {
        const response = await fetch(route('payment-receipts.lot-books', { lot: form.lot_id }));
        const data = await response.json();

        if (data.success) {
            availableBooks.value = data.books;
            // Auto set book range for 10 books from the first available book
            if (data.books && data.books.length > 0) {
                form.book_from = data.books[0].book_number;
                const maxPossibleBooks = Math.min(10, data.books.length);
                form.book_to = data.books[maxPossibleBooks - 1].book_number;
            }
        } else {
            availableBooks.value = [];
        }
    } catch (error) {
        console.error('Failed to load lot books:', error);
        availableBooks.value = [];
    } finally {
        loadingBooks.value = false;
    }
};

const submitForm = () => {
    form.post(route('payment-receipts.store-admin'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            availableBooks.value = [];
            emit('success');
            emit('close');
        },
        onError: (errors) => {
            console.error('Distribution failed:', errors);
        }
    });
};

const handleClose = () => {
    if (!form.processing) {
        form.reset();
        form.clearErrors();
        availableBooks.value = [];
        branchSearch.value = '';
        selectedBranch.value = null;
        showBranchDropdown.value = false;
        emit('close');
    }
};

// Custom directive for clicking outside
const vClickOutside = {
    mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value(event);
            }
        };
        document.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
    },
};
</script>
