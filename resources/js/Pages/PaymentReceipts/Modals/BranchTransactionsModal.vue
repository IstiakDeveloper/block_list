<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="7xl">
        <div class="bg-white rounded-lg dark:bg-gray-800">
            <!-- Header -->
            <div
                class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700"
            >
                <div>
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        {{ branch?.branch_name }} - Transaction History
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ branch?.branch_code }} | Period:
                        {{ formatDate(filters?.start_date) }} to
                        {{ formatDate(filters?.end_date) }}
                    </p>
                </div>
                <button
                    @click="$emit('close')"
                    class="p-2 text-gray-400 transition-colors rounded-lg hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <X class="w-6 h-6" />
                </button>
            </div>

            <!-- Content -->
            <div class="px-6 py-4">
                <!-- Transactions Table -->
                <div
                    v-if="transactions && transactions.length > 0"
                    class="overflow-x-auto"
                >
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                    <Calendar
                                        class="inline-block w-4 h-4 mr-1"
                                    />
                                    DATE
                                </th>
                                <th
                                    class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400"
                                >
                                    <Package
                                        class="inline-block w-4 h-4 mr-1"
                                    />
                                    Lot
                                </th>
                                <th
                                    class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Type
                                </th>
                                <th
                                    class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-400"
                                >
                                    <BookOpen
                                        class="inline-block w-4 h-4 mr-1"
                                    />
                                    Books
                                </th>
                                <th
                                    class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-400"
                                >
                                    <FileText
                                        class="inline-block w-4 h-4 mr-1"
                                    />
                                    Receipts
                                </th>
                                <th
                                    class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-400"
                                >
                                    <User class="inline-block w-4 h-4 mr-1" />
                                    Person
                                </th>
                                <th
                                    class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                        >
                            <tr
                                v-for="transaction in transactions"
                                :key="transaction.id"
                                class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <td
                                    class="px-4 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-300"
                                >
                                    {{
                                        formatDate(transaction.transaction_date)
                                    }}
                                </td>
                                <td
                                    class="px-4 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-300"
                                >
                                    <div class="font-medium">
                                        {{ transaction.lot_number }}
                                    </div>
                                    <div
                                        v-if="transaction.lot_name"
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{ transaction.lot_name }}
                                    </div>
                                </td>
                                <td
                                    class="px-4 py-4 text-center whitespace-nowrap"
                                >
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="
                                            getTypeClass(
                                                transaction.transaction_type
                                            )
                                        "
                                    >
                                        {{
                                            getTypeLabel(
                                                transaction.transaction_type
                                            )
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-4 text-sm text-center text-gray-900 whitespace-nowrap dark:text-gray-300"
                                >
                                    <div class="font-medium">
                                        {{ transaction.book_from }} -
                                        {{ transaction.book_to }}
                                    </div>
                                    <div
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        ({{ transaction.total_books }} books)
                                    </div>
                                </td>
                                <td
                                    class="px-4 py-4 text-sm font-medium text-center text-gray-900 whitespace-nowrap dark:text-gray-300"
                                >
                                    <div class="text-base">
                                        {{
                                            formatNumber(
                                                transaction.total_receipts
                                            )
                                        }}
                                    </div>
                                    <div
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{
                                            formatNumber(
                                                transaction.receipt_from
                                            )
                                        }}
                                        -
                                        {{
                                            formatNumber(transaction.receipt_to)
                                        }}
                                    </div>
                                </td>
                                <td
                                    class="px-4 py-4 text-sm text-center text-gray-900 whitespace-nowrap dark:text-gray-300"
                                >
                                    <div class="font-medium">
                                        {{
                                            transaction.given_to ||
                                            transaction.received_by ||
                                            "-"
                                        }}
                                    </div>
                                    <div
                                        v-if="transaction.pin_number"
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        PIN: {{ transaction.pin_number }}
                                    </div>
                                </td>
                                <td
                                    class="px-4 py-4 text-center whitespace-nowrap"
                                >
                                    <div
                                        class="flex items-center justify-center gap-2"
                                    >
                                        <button
                                            @click="
                                                $emit(
                                                    'view-details',
                                                    transaction.id
                                                )
                                            "
                                            class="p-1 text-blue-600 transition-colors rounded hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20"
                                            title="View Details"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="
                                                transaction.transaction_type ===
                                                'distribute_to_person'
                                            "
                                            @click="$emit('edit', transaction)"
                                            class="p-1 text-green-600 transition-colors rounded hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/20"
                                            title="Edit"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="
                                                $emit('delete', transaction.id)
                                            "
                                            class="p-1 text-red-600 transition-colors rounded hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                            title="Delete"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- No Data State -->
                <div v-else class="py-12 text-center">
                    <FileX
                        class="w-16 h-16 mx-auto mb-3 text-gray-400 opacity-50"
                    />
                    <p class="text-gray-500 dark:text-gray-400">
                        No transactions found for this period
                    </p>
                </div>

                <!-- Summary Footer -->
                <div
                    v-if="transactions && transactions.length > 0"
                    class="flex items-center justify-between p-4 mt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Total Transactions:
                        <span
                            class="font-semibold text-gray-900 dark:text-gray-100"
                            >{{ transactions.length }}</span
                        >
                    </div>
                    <div class="flex gap-6 text-sm">
                        <div>
                            <span class="text-green-600 dark:text-green-400"
                                >Received:</span
                            >
                            <span
                                class="ml-2 font-semibold text-gray-900 dark:text-gray-100"
                            >
                                {{ calculateTotal("distribute_to_branch") }}
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-600 dark:text-blue-400"
                                >Distributed:</span
                            >
                            <span
                                class="ml-2 font-semibold text-gray-900 dark:text-gray-100"
                            >
                                {{ calculateTotal("distribute_to_person") }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { computed } from "vue";
import Modal from "@/Components/Modal.vue";
import {
    X,
    FileX,
    Calendar,
    Package,
    BookOpen,
    FileText,
    User,
    Eye,
    Edit,
    Trash2,
} from "lucide-vue-next";

const props = defineProps({
    show: Boolean,
    branch: {
        type: Object,
        default: null,
    },
    transactions: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

defineEmits(["close", "refresh", "view-details", "edit", "delete"]);

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};

const formatNumber = (number) => {
    if (!number) return "0";
    return number.toLocaleString("en-US");
};

const getTypeLabel = (type) => {
    const labels = {
        distribute_to_branch: "Received",
        distribute_to_person: "Distributed",
    };
    return labels[type] || type;
};

const getTypeClass = (type) => {
    const classes = {
        distribute_to_branch:
            "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300",
        distribute_to_person:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300",
    };
    return (
        classes[type] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
    );
};

const calculateTotal = (type) => {
    if (!props.transactions) return 0;
    const total = props.transactions
        .filter((t) => t.transaction_type === type)
        .reduce((sum, t) => sum + (t.total_receipts || 0), 0);
    return formatNumber(total);
};
</script>
