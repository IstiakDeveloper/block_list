<template>
    <Modal :show="show" @close="handleClose" maxWidth="2xl">
        <div class="p-6 bg-white rounded-lg dark:bg-gray-800">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Edit Transaction
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

            <form @submit.prevent="submitForm" v-if="transaction">
                <!-- Transaction Info (Read-only) -->
                <div class="p-4 mb-6 rounded-lg bg-gray-50 dark:bg-gray-700">
                    <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Transaction Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">#{{ transaction.id }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Type:</span>
                            <span class="ml-2 font-medium">
                                <span :class="getTypeClass(transaction.transaction_type)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                    {{ getTypeLabel(transaction.transaction_type) }}
                                </span>
                            </span>
                        </div>
                        <div v-if="transaction.transaction_type !== 'stock_in'">
                            <span class="text-gray-600 dark:text-gray-400">Lot:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ transaction.lot?.lot_number }}</span>
                        </div>
                        <div v-if="transaction.transaction_type === 'distribute_to_branch'">
                            <span class="text-gray-600 dark:text-gray-400">Branch:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ transaction.branch?.branch_name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Stock In Edit Fields -->
                <div v-if="transaction.transaction_type === 'stock_in'" class="space-y-4">
                    <!-- Transaction Date -->
                    <div class="p-4 border border-purple-200 rounded-lg bg-purple-50 dark:bg-purple-900/20 dark:border-purple-700">
                        <h3 class="flex items-center gap-2 mb-4 text-sm font-semibold text-purple-900 dark:text-purple-100">
                            <Package class="w-4 h-4" />
                            Stock In Information (Editable)
                        </h3>

                        <div class="mb-4">
                            <Label value="Transaction Date" required class="text-gray-700 dark:text-gray-300" />
                            <CustomDateInput v-model="form.transaction_date" required
                                class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                        </div>

                        <!-- Lot Selection -->
                        <div class="mb-4">
                            <Label value="Lot" required class="text-gray-700 dark:text-gray-300" />
                            <select v-model="form.lot_id" required
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500">
                                <option value="">-- Select Lot --</option>
                                <option v-for="lot in activeLots" :key="lot.id" :value="lot.id">
                                    {{ lot.lot_number }} {{ lot.lot_name ? `- ${lot.lot_name}` : '' }}
                                </option>
                            </select>
                        </div>

                        <!-- Book Number Range -->
                        <div class="p-4 mb-4 border border-gray-200 rounded-lg dark:border-gray-600">
                            <h4 class="flex items-center gap-2 mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <BookOpen class="w-4 h-4" />
                                Book Number Range
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label value="From Book Number" required class="text-gray-700 dark:text-gray-300" />
                                    <Input v-model.number="form.book_from" type="number" min="1" required
                                        placeholder="e.g., 665"
                                        class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                                </div>
                                <div>
                                    <Label value="To Book Number" required class="text-gray-700 dark:text-gray-300" />
                                    <Input v-model.number="form.book_to" type="number" :min="form.book_from || 1" required
                                        placeholder="e.g., 675"
                                        class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Each book contains 100 receipts. Book 665 = Receipts 66401-66500
                            </p>
                        </div>

                        <!-- Auto Calculate Display -->
                        <div v-if="form.book_from && form.book_to && form.book_to >= form.book_from"
                            class="p-4 border-2 border-purple-200 rounded-lg bg-purple-50 dark:bg-purple-900/20 dark:border-purple-700">
                            <div class="flex items-center gap-2 mb-3">
                                <Calculator class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-100">
                                    Auto Calculated:
                                </h4>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg dark:bg-gray-800">
                                    <span class="text-sm text-purple-700 dark:text-purple-300">Total Books:</span>
                                    <span class="text-lg font-bold text-purple-900 dark:text-purple-100">{{ totalBooks }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg dark:bg-gray-800">
                                    <span class="text-sm text-purple-700 dark:text-purple-300">Total Receipts:</span>
                                    <span class="text-lg font-bold text-purple-900 dark:text-purple-100">{{ totalReceipts }}</span>
                                </div>
                                <div class="col-span-2 p-3 bg-white rounded-lg dark:bg-gray-800">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-purple-700 dark:text-purple-300">Receipt Range:</span>
                                        <span class="text-base font-bold text-purple-900 dark:text-purple-100">
                                            {{ receiptFrom }} - {{ receiptTo }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Received By -->
                        <div class="mt-4">
                            <Label value="Received By" class="text-gray-700 dark:text-gray-300" />
                            <Input v-model="form.received_by" type="text"
                                class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                        </div>
                    </div>
                </div>

                <!-- Distribute to Person Edit Fields -->
                <div v-else-if="transaction.transaction_type === 'distribute_to_person'" class="space-y-4">
                    <!-- Transaction Date -->
                    <div class="mb-4">
                        <Label value="Transaction Date" required class="text-gray-700 dark:text-gray-300" />
                        <CustomDateInput v-model="form.transaction_date" required
                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                    </div>

                    <div class="p-4 border border-green-200 rounded-lg bg-green-50 dark:bg-green-900/20 dark:border-green-700">
                        <h3 class="flex items-center gap-2 mb-4 text-sm font-semibold text-green-900 dark:text-green-100">
                            <User class="w-4 h-4" />
                            Person Information (Editable)
                        </h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <Label value="Given To" required class="text-gray-700 dark:text-gray-300" />
                                <Input v-model="form.given_to" type="text" required
                                    class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                            </div>
                            <div>
                                <Label value="PIN Number" class="text-gray-700 dark:text-gray-300" />
                                <Input v-model="form.pin_number" type="text"
                                    class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribute to Branch Edit Fields -->
                <div v-else-if="transaction.transaction_type === 'distribute_to_branch'" class="space-y-4">
                    <div class="p-4 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-700">
                        <h3 class="flex items-center gap-2 mb-4 text-sm font-semibold text-blue-900 dark:text-blue-100">
                            <Building class="w-4 h-4" />
                            Branch Distribution Information (Editable)
                        </h3>

                        <!-- Transaction Date -->
                        <div class="mb-4">
                            <Label value="Transaction Date" required class="text-gray-700 dark:text-gray-300" />
                            <CustomDateInput v-model="form.transaction_date" required
                                class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                        </div>

                        <!-- Lot Selection -->
                        <div class="mb-4">
                            <Label value="Lot" required class="text-gray-700 dark:text-gray-300" />
                            <select v-model="form.lot_id" required
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Select Lot --</option>
                                <option v-for="lot in activeLots" :key="lot.id" :value="lot.id">
                                    {{ lot.lot_number }} {{ lot.lot_name ? `- ${lot.lot_name}` : '' }}
                                </option>
                            </select>
                        </div>

                        <!-- Book Number Range -->
                        <div class="p-4 mb-4 border border-gray-200 rounded-lg dark:border-gray-600">
                            <h4 class="flex items-center gap-2 mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <BookOpen class="w-4 h-4" />
                                Book Number Range
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label value="From Book Number" required class="text-gray-700 dark:text-gray-300" />
                                    <Input v-model.number="form.book_from" type="number" min="1" required
                                        placeholder="e.g., 665"
                                        class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                                </div>
                                <div>
                                    <Label value="To Book Number" required class="text-gray-700 dark:text-gray-300" />
                                    <Input v-model.number="form.book_to" type="number" :min="form.book_from || 1" required
                                        placeholder="e.g., 675"
                                        class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Each book contains 100 receipts. Book 665 = Receipts 66401-66500
                            </p>
                        </div>

                        <!-- Auto Calculate Display -->
                        <div v-if="form.book_from && form.book_to && form.book_to >= form.book_from"
                            class="p-4 mb-4 border-2 border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-700">
                            <div class="flex items-center gap-2 mb-3">
                                <Calculator class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                                    Auto Calculated:
                                </h4>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg dark:bg-gray-800">
                                    <span class="text-sm text-blue-700 dark:text-blue-300">Total Books:</span>
                                    <span class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ totalBooks }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg dark:bg-gray-800">
                                    <span class="text-sm text-blue-700 dark:text-blue-300">Total Receipts:</span>
                                    <span class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ totalReceipts }}</span>
                                </div>
                                <div class="col-span-2 p-3 bg-white rounded-lg dark:bg-gray-800">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-blue-700 dark:text-blue-300">Receipt Range:</span>
                                        <span class="text-base font-bold text-blue-900 dark:text-blue-100">
                                            {{ receiptFrom }} - {{ receiptTo }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Received By -->
                        <div>
                            <Label value="Received By" class="text-gray-700 dark:text-gray-300" />
                            <Input v-model="form.received_by" type="text"
                                class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Person who received the books at the branch
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Remarks (Optional) -->
                <div class="mt-4">
                    <Label value="Remarks (Optional)" class="text-gray-700 dark:text-gray-300" />
                    <textarea v-model="form.remarks" rows="3"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Add any notes or remarks..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <SecondaryButton @click="handleClose" type="button" :disabled="form.processing">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="form.processing || !isFormValid">
                        <Loader2 v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                        <Save v-else class="w-4 h-4 mr-2" />
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import CustomDateInput from '@/Components/CustomDateInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Loader2, X, AlertCircle, User, Building, Save, Package, BookOpen, Calculator } from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    transaction: {
        type: Object,
        default: null
    },
    activeLots: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'success']);

const form = useForm({
    transaction_date: '',
    lot_id: '',
    book_from: null,
    book_to: null,
    given_to: '',
    pin_number: '',
    received_by: '',
    remarks: ''
});

// Watch for transaction changes to populate form
watch(() => props.transaction, (newTransaction) => {
    if (newTransaction) {
        // Common fields - format date properly
        if (newTransaction.transaction_date) {
            // Extract date from ISO format (e.g., "2025-12-22T18:00:00.000000Z" -> "2025-12-22")
            const dateStr = newTransaction.transaction_date.split('T')[0];
            form.transaction_date = dateStr;
        } else {
            form.transaction_date = '';
        }
        form.remarks = newTransaction.remarks || '';

        // Stock In specific fields
        if (newTransaction.transaction_type === 'stock_in') {
            form.lot_id = newTransaction.lot_id || '';
            form.book_from = newTransaction.book_from || null;
            form.book_to = newTransaction.book_to || null;
            form.received_by = newTransaction.received_by || '';
        }
        // Person distribution fields
        else if (newTransaction.transaction_type === 'distribute_to_person') {
            form.given_to = newTransaction.given_to || '';
            form.pin_number = newTransaction.pin_number || '';
        }
        // Branch distribution fields
        else if (newTransaction.transaction_type === 'distribute_to_branch') {
            form.lot_id = newTransaction.lot_id || '';
            form.book_from = newTransaction.book_from || null;
            form.book_to = newTransaction.book_to || null;
            form.received_by = newTransaction.received_by || '';
        }
    }
}, { immediate: true });

// Computed properties for Stock In calculations
const totalBooks = computed(() => {
    if (!form.book_from || !form.book_to || form.book_to < form.book_from) return 0;
    return form.book_to - form.book_from + 1;
});

const totalReceipts = computed(() => totalBooks.value * 100);

const receiptFrom = computed(() => {
    if (!form.book_from) return 0;
    return (form.book_from - 1) * 100 + 1;
});

const receiptTo = computed(() => {
    if (!form.book_to) return 0;
    return form.book_to * 100;
});

const isFormValid = computed(() => {
    if (!props.transaction) return false;

    // Validate transaction date
    if (!form.transaction_date) return false;

    // Stock In specific validation
    if (props.transaction.transaction_type === 'stock_in') {
        if (!form.lot_id) return false;
        if (!form.book_from || !form.book_to) return false;
        if (form.book_to < form.book_from) return false;
    }
    // Branch distribution validation
    else if (props.transaction.transaction_type === 'distribute_to_branch') {
        if (!form.lot_id) return false;
        if (!form.book_from || !form.book_to) return false;
        if (form.book_to < form.book_from) return false;
    }
    // Person distribution validation
    else if (props.transaction.transaction_type === 'distribute_to_person') {
        if (!form.given_to) return false;
    }

    return true;
});

const getTypeLabel = (type) => {
    const labels = {
        'stock_in': 'Stock In',
        'distribute_to_branch': 'To Branch',
        'distribute_to_person': 'To Person'
    };
    return labels[type] || type;
};

const getTypeClass = (type) => {
    const classes = {
        'stock_in': 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
        'distribute_to_branch': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        'distribute_to_person': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
    };
    return classes[type] || 'bg-gray-100 text-gray-800';
};

const submitForm = () => {
    if (!props.transaction) return;

    form.put(route('payment-receipts.update', { receipt: props.transaction.id }), {
        preserveScroll: true,
        onSuccess: () => {
            emit('success');
            emit('close');
        },
        onError: (errors) => {
            console.error('Update failed:', errors);
        }
    });
};

const handleClose = () => {
    if (!form.processing) {
        form.reset();
        form.clearErrors();
        emit('close');
    }
};
</script>
