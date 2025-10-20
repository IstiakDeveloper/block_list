<template>
    <Modal :show="show" @close="handleClose" maxWidth="xl">
        <div class="p-4 bg-white rounded-lg dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h2
                    class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                >
                    Add Stock to Head Office
                </h2>
                <button
                    @click="handleClose"
                    type="button"
                    class="p-2 text-gray-400 transition-colors rounded-lg hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Error Display -->
            <div
                v-if="form.errors && Object.keys(form.errors).length > 0"
                class="flex items-start gap-2 p-4 mb-4 border border-red-400 rounded-lg bg-red-50 dark:bg-red-900/30 dark:border-red-500"
            >
                <AlertCircle
                    class="w-5 h-5 mt-0.5 text-red-600 dark:text-red-400 flex-shrink-0"
                />
                <div class="space-y-1">
                    <div
                        v-for="(error, key) in form.errors"
                        :key="key"
                        class="text-sm text-red-600 dark:text-red-400"
                    >
                        {{ error }}
                    </div>
                </div>
            </div>

            <form @submit.prevent="submitForm">
                <!-- Lot Selection Type -->
                <div class="mb-6">
                    <Label
                        value="Lot Option"
                        required
                        class="mb-2 text-gray-700 dark:text-gray-300"
                    />
                    <div class="flex gap-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="radio"
                                v-model="form.lot_option"
                                value="existing"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                            />
                            <span
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                >Use Existing Lot</span
                            >
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="radio"
                                v-model="form.lot_option"
                                value="new"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                            />
                            <span
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                >Create New Lot</span
                            >
                        </label>
                    </div>
                </div>

                <!-- Existing Lot Selection -->
                <div v-if="form.lot_option === 'existing'" class="mb-6">
                    <Label
                        value="Select Lot"
                        required
                        class="text-gray-700 dark:text-gray-300"
                    />
                    <select
                        v-model="form.lot_id"
                        required
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">-- Select a lot --</option>
                        <option
                            v-for="lot in activeLots"
                            :key="lot.id"
                            :value="lot.id"
                        >
                            {{ lot.lot_number }}
                            {{ lot.lot_name ? `- ${lot.lot_name}` : "" }}
                            ({{ lot.available_books_count || 0 }} books
                            available)
                        </option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Select from existing lots that already have books
                    </p>
                </div>

                <!-- New Lot Name -->
                <div v-if="form.lot_option === 'new'" class="mb-6">
                    <Label
                        value="Lot Name (Optional)"
                        class="text-gray-700 dark:text-gray-300"
                    />
                    <Input
                        v-model="form.lot_name"
                        type="text"
                        placeholder="e.g., January 2025 Batch"
                        class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                    />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        A new lot will be created automatically (e.g., Lot-1,
                        Lot-2)
                    </p>
                </div>

                <!-- Book Number Range -->
                <div
                    class="p-4 mb-6 border border-gray-200 rounded-lg dark:border-gray-600"
                >
                    <h3
                        class="flex items-center gap-2 mb-4 text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        <Package class="w-4 h-4" />
                        Book Number Range
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <Label
                                value="From Book Number"
                                required
                                class="text-gray-700 dark:text-gray-300"
                            />
                            <Input
                                v-model.number="form.book_from"
                                type="number"
                                min="1"
                                required
                                placeholder="e.g., 665"
                                class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                            />
                        </div>
                        <div>
                            <Label
                                value="To Book Number"
                                required
                                class="text-gray-700 dark:text-gray-300"
                            />
                            <Input
                                v-model.number="form.book_to"
                                type="number"
                                :min="form.book_from || 1"
                                required
                                placeholder="e.g., 675"
                                class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                            />
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Each book contains 100 receipts. Book 665 = Receipts
                        66501-66600
                    </p>
                </div>

                <!-- Auto Calculate Display -->
                <div
                    v-if="
                        form.book_from &&
                        form.book_to &&
                        form.book_to >= form.book_from
                    "
                    class="p-4 mb-6 border-2 border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-700"
                >
                    <div class="flex items-center gap-2 mb-3">
                        <Calculator
                            class="w-5 h-5 text-blue-600 dark:text-blue-400"
                        />
                        <h4
                            class="text-sm font-semibold text-blue-900 dark:text-blue-100"
                        >
                            Auto Calculated:
                        </h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg dark:bg-gray-800"
                        >
                            <span
                                class="text-sm text-blue-700 dark:text-blue-300"
                                >Total Books:</span
                            >
                            <span
                                class="text-lg font-bold text-blue-900 dark:text-blue-100"
                                >{{ totalBooks }}</span
                            >
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg dark:bg-gray-800"
                        >
                            <span
                                class="text-sm text-blue-700 dark:text-blue-300"
                                >Total Receipts:</span
                            >
                            <span
                                class="text-lg font-bold text-blue-900 dark:text-blue-100"
                                >{{ totalReceipts }}</span
                            >
                        </div>
                        <div
                            class="col-span-2 p-3 bg-white rounded-lg dark:bg-gray-800"
                        >
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm text-blue-700 dark:text-blue-300"
                                    >Receipt Range:</span
                                >
                                <span
                                    class="text-base font-bold text-blue-900 dark:text-blue-100"
                                >
                                    {{ receiptFrom }} - {{ receiptTo }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3">
                    <SecondaryButton
                        @click="handleClose"
                        type="button"
                        :disabled="form.processing"
                    >
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="!isFormValid || form.processing">
                        <Loader2
                            v-if="form.processing"
                            class="w-4 h-4 mr-2 animate-spin"
                        />
                        <PackagePlus v-else class="w-4 h-4 mr-2" />
                        {{ form.processing ? "Adding Stock..." : "Add Stock" }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {
    Loader2,
    X,
    AlertCircle,
    Package,
    Calculator,
    PackagePlus,
} from "lucide-vue-next";

const props = defineProps({
    show: Boolean,
    activeLots: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["close", "success"]);

const form = useForm({
    lot_option: "existing",
    lot_id: "",
    lot_name: "",
    book_from: null,
    book_to: null,
});

// Watch for lot option changes
watch(
    () => form.lot_option,
    (newVal) => {
        if (newVal === "existing") {
            form.lot_name = "";
        } else {
            form.lot_id = "";
        }
    }
);

const totalBooks = computed(() => {
    if (!form.book_from || !form.book_to || form.book_to < form.book_from)
        return 0;
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
    if (form.lot_option === "existing" && !form.lot_id) return false;
    if (form.lot_option === "new" && !form.lot_name) return false;
    if (!form.book_from || !form.book_to) return false;
    if (form.book_to < form.book_from) return false;
    return true;
});

const submitForm = () => {
    form.post(route("payment-receipts.stock-in"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.lot_option = "existing";
            emit("success");
            emit("close");
        },
        onError: (errors) => {
            console.error("Stock add failed:", errors);
        },
    });
};

const handleClose = () => {
    if (!form.processing) {
        form.reset();
        form.clearErrors();
        form.lot_option = "existing";
        emit("close");
    }
};
</script>
