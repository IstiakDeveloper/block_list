<template>
    <Modal :show="show" @close="handleClose" maxWidth="2xl">
        <div class="p-4 bg-white rounded-lg dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Distribute to Person
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
                <!-- Person Info Section -->
                <div class="p-4 mb-6 border border-purple-200 rounded-lg bg-purple-50 dark:bg-purple-900/20 dark:border-purple-700">
                    <h3 class="flex items-center gap-2 mb-4 text-sm font-semibold text-purple-900 dark:text-purple-100">
                        <User class="w-4 h-4" />
                        Person Information
                    </h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <Label value="Person Name" required class="text-gray-700 dark:text-gray-300" />
                            <Input v-model="form.given_to" type="text" required
                                placeholder="Enter person name"
                                class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                        </div>
                        <div>
                            <Label value="PIN Number (Optional)" class="text-gray-700 dark:text-gray-300" />
                            <Input v-model="form.pin_number" type="text"
                                placeholder="Enter PIN number"
                                class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                        </div>
                    </div>
                </div>

                <!-- Next Available Book (Auto Selected) -->
                <div class="mb-6">
                    <h3 class="flex items-center gap-2 mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        <Package class="w-4 h-4" />
                        Next Available Book (Auto Selected)
                    </h3>

                    <div v-if="nextBook"
                        class="p-6 border-2 border-indigo-500 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 ring-2 ring-indigo-500">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xl font-bold text-indigo-900 dark:text-indigo-100">
                                        Book #{{ nextBook.book_number }}
                                    </h4>
                                    <span class="px-2 py-1 text-xs font-semibold text-indigo-700 bg-indigo-200 rounded-full dark:bg-indigo-800 dark:text-indigo-200">
                                        Next in Queue
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-indigo-700 dark:text-indigo-300">{{ nextLot.lot_number }} - {{ nextLot.lot_name || 'N/A' }}</p>
                            </div>
                            <CheckCircle class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-2 bg-white rounded-lg dark:bg-gray-800">
                                <div class="text-xs text-indigo-700 dark:text-indigo-300">Receipt Range</div>
                                <div class="text-sm font-bold text-indigo-900 dark:text-indigo-100">
                                    {{ formatNumber(nextBook.from_number) }} - {{ formatNumber(nextBook.to_number) }}
                                </div>
                            </div>
                            <div class="p-2 bg-white rounded-lg dark:bg-gray-800">
                                <div class="text-xs text-indigo-700 dark:text-indigo-300">Total Receipts</div>
                                <div class="text-sm font-bold text-indigo-900 dark:text-indigo-100">100</div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-12 text-center text-gray-500 dark:text-gray-400">
                        <Package class="w-16 h-16 mx-auto mb-3 opacity-50" />
                        <p class="font-medium">No stock available</p>
                        <p class="text-sm">Please receive books from head office first</p>
                    </div>
                </div>

                <!-- Info Message -->
                <div class="flex items-start gap-2 p-3 mb-6 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-700">
                    <Info class="w-5 h-5 mt-0.5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Books will be distributed in serial order automatically. Each person receives 1 book (100 receipts).
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="handleClose" type="button" :disabled="form.processing">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="!isFormValid || form.processing">
                        <Loader2 v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                        <SendIcon v-else class="w-4 h-4 mr-2" />
                        {{ form.processing ? 'Distributing...' : 'Distribute 1 Book (100 Receipts)' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {
    Loader2, X, AlertCircle, Package, SendIcon, User, CheckCircle, Info
} from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    availableBooks: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'success']);

const form = useForm({
    given_to: '',
    pin_number: '',
    quantity: 100 // Always 1 book = 100 receipts
});

// Get next available book (first book in serial order)
const nextLot = computed(() => {
    if (!props.availableBooks || props.availableBooks.length === 0) return null;
    return props.availableBooks[0]; // First lot
});

const nextBook = computed(() => {
    if (!nextLot.value || !nextLot.value.books) return null;
    return nextLot.value.books[0]; // First book in first lot
});

const isFormValid = computed(() => {
    if (!form.given_to) return false;
    if (!nextBook.value) return false;
    return true;
});

// Methods
const formatNumber = (number) => {
    if (!number) return '0';
    return number.toLocaleString('en-US');
};

const submitForm = () => {
    if (!isFormValid.value) return;

    form.post(route('payment-receipts.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.quantity = 100; // Reset to default
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
        form.quantity = 100;
        emit('close');
    }
};
</script>
