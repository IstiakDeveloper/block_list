<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="2xl">
        <div class="p-4 bg-white rounded-lg dark:bg-gray-800">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <h2
                    class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                >
                    Transaction Details
                </h2>
                <button
                    @click="$emit('close')"
                    class="p-2 text-gray-400 transition-colors rounded-lg hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>

            <div v-if="transaction" class="space-y-4">
                <!-- Basic Info -->
                <div
                    class="grid grid-cols-2 gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700"
                >
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Transaction ID
                        </div>
                        <div
                            class="text-sm font-medium text-gray-900 dark:text-gray-100"
                        >
                            #{{ transaction.id }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Date
                        </div>
                        <div
                            class="font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ transaction.transaction_date }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Type
                        </div>
                        <span
                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                            :class="getTypeClass(transaction.transaction_type)"
                        >
                            {{ getTypeLabel(transaction.transaction_type) }}
                        </span>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Branch
                        </div>
                        <div
                            class="font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ transaction.branch_name || "Head Office" }}
                        </div>
                    </div>
                </div>

                <!-- Lot Info -->
                <div
                    class="p-4 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/30 dark:border-blue-700"
                >
                    <h3
                        class="mb-3 text-sm font-semibold text-blue-900 dark:text-blue-100"
                    >
                        <Package class="inline-block w-4 h-4 mr-1" />
                        Lot Information
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700 dark:text-blue-300"
                                >Lot Number:</span
                            >
                            <span
                                class="ml-2 font-semibold text-blue-900 dark:text-blue-100"
                            >
                                {{ transaction.lot_number }}
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300"
                                >Lot Name:</span
                            >
                            <span
                                class="ml-2 font-semibold text-blue-900 dark:text-blue-100"
                            >
                                {{ transaction.lot_name || "N/A" }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Books & Receipts Info -->
                <div
                    class="p-4 border border-green-200 rounded-lg bg-green-50 dark:bg-green-900/30 dark:border-green-700"
                >
                    <h3
                        class="mb-3 text-sm font-semibold text-green-900 dark:text-green-100"
                    >
                        <BookOpen class="inline-block w-4 h-4 mr-1" />
                        Books & Receipts
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-green-700 dark:text-green-300"
                                >Book Range:</span
                            >
                            <span
                                class="ml-2 font-semibold text-green-900 dark:text-green-100"
                            >
                                {{ transaction.book_from }} -
                                {{ transaction.book_to }}
                            </span>
                        </div>
                        <div>
                            <span class="text-green-700 dark:text-green-300"
                                >Total Books:</span
                            >
                            <span
                                class="ml-2 font-semibold text-green-900 dark:text-green-100"
                            >
                                {{ transaction.total_books }}
                            </span>
                        </div>
                        <div>
                            <span class="text-green-700 dark:text-green-300"
                                >Receipt Range:</span
                            >
                            <span
                                class="ml-2 font-semibold text-green-900 dark:text-green-100"
                            >
                                {{ formatNumber(transaction.receipt_from) }} -
                                {{ formatNumber(transaction.receipt_to) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-green-700 dark:text-green-300"
                                >Total Receipts:</span
                            >
                            <span
                                class="ml-2 font-semibold text-green-900 dark:text-green-100"
                            >
                                {{ formatNumber(transaction.total_receipts) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Person Info -->
                <div
                    v-if="transaction.given_to || transaction.received_by"
                    class="p-4 border border-purple-200 rounded-lg bg-purple-50 dark:bg-purple-900/30 dark:border-purple-700"
                >
                    <h3
                        class="mb-3 text-sm font-semibold text-purple-900 dark:text-purple-100"
                    >
                        <User class="inline-block w-4 h-4 mr-1" />
                        Person Details
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div v-if="transaction.given_to">
                            <span class="text-purple-700 dark:text-purple-300"
                                >Given To:</span
                            >
                            <span
                                class="ml-2 font-semibold text-purple-900 dark:text-purple-100"
                            >
                                {{ transaction.given_to }}
                            </span>
                        </div>
                        <div v-if="transaction.pin_number">
                            <span class="text-purple-700 dark:text-purple-300"
                                >PIN Number:</span
                            >
                            <span
                                class="ml-2 font-semibold text-purple-900 dark:text-purple-100"
                            >
                                {{ transaction.pin_number }}
                            </span>
                        </div>
                        <div v-if="transaction.received_by">
                            <span class="text-purple-700 dark:text-purple-300"
                                >Received By:</span
                            >
                            <span
                                class="ml-2 font-semibold text-purple-900 dark:text-purple-100"
                            >
                                {{ transaction.received_by }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Individual Books List -->
                <div
                    v-if="transaction.books && transaction.books.length > 0"
                    class="p-4 border border-gray-200 rounded-lg dark:border-gray-600"
                >
                    <h3
                        class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100"
                    >
                        <List class="inline-block w-4 h-4 mr-1" />
                        Individual Books ({{ transaction.books.length }})
                    </h3>
                    <div
                        class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4"
                    >
                        <div
                            v-for="book in transaction.books"
                            :key="book.book_number"
                            class="p-3 text-sm border border-gray-200 rounded-lg dark:border-gray-600 dark:bg-gray-700"
                        >
                            <div
                                class="font-medium text-gray-900 dark:text-gray-100"
                            >
                                Book #{{ book.book_number }}
                            </div>
                            <div
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                {{ formatNumber(book.from_number) }} -
                                {{ formatNumber(book.to_number) }}
                            </div>
                            <div
                                class="mt-1 text-xs font-medium text-blue-600 dark:text-blue-400"
                            >
                                {{ book.receipts }} receipts
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div
                    v-if="transaction.remarks"
                    class="p-4 border border-yellow-200 rounded-lg bg-yellow-50 dark:bg-yellow-900/30 dark:border-yellow-700"
                >
                    <h3
                        class="mb-2 text-sm font-semibold text-yellow-900 dark:text-yellow-100"
                    >
                        <FileText class="inline-block w-4 h-4 mr-1" />
                        Remarks
                    </h3>
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        {{ transaction.remarks }}
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <SecondaryButton @click="$emit('close')" type="button">
                        Close
                    </SecondaryButton>

                    <button
                        v-if="isSuperAdmin"
                        @click="$emit('edit', transaction)"
                        class="inline-flex items-center gap-2 px-4 py-2 text-white transition-colors bg-blue-600 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800"
                    >
                        <Edit class="w-4 h-4" />
                        Edit
                    </button>

                    <button
                        v-if="isSuperAdmin"
                        @click="$emit('delete', transaction.id)"
                        class="inline-flex items-center gap-2 px-4 py-2 text-white transition-colors bg-red-600 rounded-md hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:bg-red-700 dark:hover:bg-red-600 dark:focus:ring-offset-gray-800"
                    >
                        <Trash2 class="w-4 h-4" />
                        Delete
                    </button>
                </div>
            </div>

            <!-- No Data State -->
            <div
                v-else
                class="py-8 text-center text-gray-500 dark:text-gray-400"
            >
                <FileX class="w-16 h-16 mx-auto mb-3 opacity-50" />
                <p>No transaction details available</p>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { computed } from "vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {
    X,
    Package,
    BookOpen,
    User,
    List,
    FileText,
    FileX,
    Edit,
    Trash2,
} from "lucide-vue-next";

const props = defineProps({
    show: Boolean,
    transaction: {
        type: Object,
        default: null,
    },
    auth: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['close', 'edit', 'delete']);

const isSuperAdmin = computed(() => props.auth?.user?.name === "Super Admin");

const canEdit = computed(() => {
    if (!props.transaction) return false;
    if (isSuperAdmin.value) return true;
    return false;
});

const formatNumber = (number) => {
    if (!number) return "0";
    return number.toLocaleString("en-US");
};

const getTypeLabel = (type) => {
    const labels = {
        stock_in: "Stock In",
        distribute_to_branch: "To Branch",
        distribute_to_person: "To Person",
    };
    return labels[type] || type;
};

const getTypeClass = (type) => {
    const classes = {
        stock_in:
            "bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300",
        distribute_to_branch:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300",
        distribute_to_person:
            "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300",
    };
    return (
        classes[type] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
    );
};
</script>
