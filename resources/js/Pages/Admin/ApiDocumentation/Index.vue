<template>
    <Head title="API Documentation" />

    <AdminLayout>
        <div class="container mx-auto px-4 py-8 max-w-5xl space-y-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100">API Documentation & Settings</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    MisLoan ইন্টিগ্রেশন — Admin থেকে token তৈরি ও ম্যানেজ করুন
                </p>
            </div>

            <!-- Token panel -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-indigo-100 dark:border-indigo-900/40">
                <div class="px-5 py-4 bg-gradient-to-r from-indigo-600 to-blue-700 text-white">
                    <h2 class="text-lg font-bold">MisLoan API Token</h2>
                    <p class="text-indigo-100 text-sm mt-0.5">এক ক্লিকে token তৈরি → MisLoan .env-এ কপি করুন</p>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid sm:grid-cols-3 gap-3 text-sm">
                        <div class="rounded-lg bg-slate-50 dark:bg-gray-900/50 p-3 border border-slate-200 dark:border-gray-700">
                            <p class="text-xs font-semibold text-slate-500 uppercase">Token স্ট্যাটাস</p>
                            <p class="mt-1 font-semibold" :class="misloan.token_active ? 'text-emerald-600' : 'text-amber-600'">
                                {{ misloan.token_active ? '● সক্রিয়' : '○ নেই — তৈরি করুন' }}
                            </p>
                            <p v-if="misloan.token_created_at" class="text-xs text-slate-500 mt-1">তৈরি: {{ misloan.token_created_at }}</p>
                            <p v-if="misloan.token_last_used_at" class="text-xs text-slate-500">শেষ ব্যবহার: {{ misloan.token_last_used_at }}</p>
                        </div>
                        <div class="rounded-lg bg-slate-50 dark:bg-gray-900/50 p-3 border border-slate-200 dark:border-gray-700">
                            <p class="text-xs font-semibold text-slate-500 uppercase">Integration User</p>
                            <p class="mt-1 font-mono text-sm" :class="integration.user_found ? 'text-slate-800 dark:text-slate-200' : 'text-rose-600'">
                                {{ integration.username }}
                            </p>
                            <p v-if="integration.user_name" class="text-xs text-slate-500">{{ integration.user_name }}</p>
                            <p v-if="!integration.user_found" class="text-xs text-rose-600 mt-1">এই username block_list-এ নেই!</p>
                        </div>
                        <div class="rounded-lg bg-slate-50 dark:bg-gray-900/50 p-3 border border-slate-200 dark:border-gray-700 space-y-1">
                            <p class="text-xs font-semibold text-slate-500 uppercase">URLs</p>
                            <p class="font-mono text-[10px] break-all text-slate-700 dark:text-slate-300">API: {{ apiBaseUrl }}</p>
                            <p class="font-mono text-[10px] break-all text-slate-700 dark:text-slate-300">MisLoan: {{ misloanAppUrl }}</p>
                        </div>
                    </div>

                    <!-- New token display -->
                    <div v-if="misloan.plain_token" class="rounded-xl border-2 border-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 p-4 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-bold text-emerald-800 dark:text-emerald-300">
                                ✓ নতুন token তৈরি হয়েছে — এখনই কপি করুন
                            </p>
                            <button type="button" @click="clearTokenDisplay" class="text-xs text-slate-500 hover:text-slate-700">লুকান</button>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input
                                ref="tokenInput"
                                :value="misloan.plain_token"
                                readonly
                                class="flex-1 font-mono text-xs px-3 py-2.5 rounded-lg border-2 border-emerald-300 bg-white dark:bg-gray-900 select-all"
                                @focus="($event.target as HTMLInputElement).select()"
                            />
                            <button type="button" @click="copyText(misloan.plain_token, 'token')" class="btn-primary whitespace-nowrap px-5">
                                {{ copied === 'token' ? '✓ কপি হয়েছে' : 'Token কপি' }}
                            </button>
                        </div>
                        <div class="rounded-lg bg-white dark:bg-gray-900 p-3 font-mono text-xs whitespace-pre-wrap border border-emerald-200 text-slate-800 dark:text-slate-200">{{ envBlock }}</div>
                        <button type="button" @click="copyText(envBlock, 'env')" class="text-sm font-semibold text-emerald-700 hover:underline">
                            {{ copied === 'env' ? '✓ .env ব্লক কপি হয়েছে' : '.env ব্লক কপি করুন' }}
                        </button>
                    </div>

                    <div
                        v-if="!integration.user_found"
                        class="rounded-lg border-2 border-rose-300 bg-rose-50 dark:bg-rose-950/30 p-4 text-sm text-rose-800 dark:text-rose-200"
                    >
                        <p class="font-bold">Token তৈরি করা যাচ্ছে না</p>
                        <p class="mt-1">
                            <code class="font-mono">{{ integration.username }}</code> username-এর user block_list-এ নেই।
                            Admin → Users থেকে এই username যোগ করুন, অথবা <code>.env</code>-এ
                            <code>MISLOAN_INTEGRATION_USERNAME</code> সেট করুন।
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-3 pt-1">
                        <button
                            type="button"
                            @click="generateToken"
                            :disabled="generating || !integration.user_found"
                            class="btn-primary text-base disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                        >
                            {{ generating ? 'তৈরি হচ্ছে...' : (misloan.token_active ? '🔄 নতুন Token তৈরি' : '🔑 Token তৈরি করুন') }}
                        </button>
                        <button
                            v-if="misloan.token_active"
                            type="button"
                            @click="revokeToken"
                            :disabled="revoking"
                            class="px-4 py-2 rounded-lg border border-rose-300 text-rose-700 text-sm font-semibold hover:bg-rose-50 disabled:opacity-50"
                        >
                            {{ revoking ? 'বাতিল...' : 'Token বাতিল' }}
                        </button>
                        <button
                            type="button"
                            @click="testConnection"
                            :disabled="testing"
                            class="px-4 py-2 rounded-lg border border-sky-300 text-sky-700 text-sm font-semibold hover:bg-sky-50 disabled:opacity-50"
                        >
                            {{ testing ? 'পরীক্ষা...' : 'API সংযোগ পরীক্ষা' }}
                        </button>
                    </div>

                    <div class="rounded-lg bg-slate-50 dark:bg-gray-900/40 border border-slate-200 dark:border-gray-700 p-4 text-xs text-slate-600 dark:text-slate-400 space-y-2">
                        <p class="font-bold text-slate-700 dark:text-slate-300">কীভাবে ব্যবহার করবেন:</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li><strong>Token তৈরি করুন</strong> বাটনে ক্লিক করুন</li>
                            <li>সবুজ বক্স থেকে token কপি করুন</li>
                            <li><a :href="misloanAppUrl" target="_blank" class="text-indigo-600 underline">{{ misloanAppUrl }}</a> সার্ভারের <code>.env</code>-এ বসান</li>
                            <li>MisLoan-এ config cache clear করুন</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- API docs -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">API Endpoints</h2>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div v-for="ep in endpoints" :key="ep.title" class="rounded-lg border border-slate-200 dark:border-gray-700 p-4 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded" :class="ep.method === 'GET' ? 'bg-sky-100 text-sky-800' : 'bg-amber-100 text-amber-800'">{{ ep.method }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ ep.title }}</span>
                            <span v-if="ep.auth" class="text-[10px] bg-violet-100 text-violet-800 px-2 py-0.5 rounded font-bold">Bearer Token</span>
                        </div>
                        <p v-if="ep.description" class="text-slate-600 dark:text-slate-400 text-xs">{{ ep.description }}</p>
                        <code class="block text-xs font-mono bg-slate-50 dark:bg-gray-900 p-2 rounded break-all">{{ ep.path }}</code>
                        <pre v-if="ep.body" class="text-xs font-mono bg-slate-50 dark:bg-gray-900 p-2 rounded overflow-x-auto">{{ ep.body }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    apiBaseUrl: string;
    misloanAppUrl: string;
    integration: { username: string; user_found: boolean; user_name?: string | null };
    misloan: {
        env_url_key: string;
        env_token_key: string;
        env_url_value: string;
        token_active: boolean;
        token_created_at?: string | null;
        token_last_used_at?: string | null;
        plain_token?: string | null;
    };
}>();

const generating = ref(false);
const revoking = ref(false);
const testing = ref(false);
const copied = ref<'token' | 'env' | null>(null);

const envBlock = computed(() =>
    `${props.misloan.env_url_key}=${props.misloan.env_url_value}\n${props.misloan.env_token_key}=${props.misloan.plain_token || 'YOUR_TOKEN_HERE'}`
);

const endpoints = computed(() => [
    {
        title: 'Verify Username (MisLoan)',
        method: 'GET',
        path: `${props.apiBaseUrl}/users/verify?username=xxx&branch_code=0003`,
        auth: true,
        description: 'Approver username ও branch code যাচাই।',
    },
    {
        title: 'Add Block List Customer',
        method: 'POST',
        path: `${props.apiBaseUrl}/customers`,
        auth: true,
        description: 'MisLoan reject → block list entry।',
        body: JSON.stringify({
            blocked_by_username: 'approver_username',
            branch_code: '0003',
            name: 'Member Name',
            nid_number: '1234567890123',
            phone_number: '01712345678',
            details: 'Reject reason',
        }, null, 2),
    },
    {
        title: 'Login',
        method: 'POST',
        path: `${props.apiBaseUrl}/auth/login`,
        auth: false,
        body: JSON.stringify({ login: 'superadmin', password: '********' }, null, 2),
    },
    {
        title: 'Get Branches',
        method: 'GET',
        path: `${props.apiBaseUrl}/branches?all=true`,
        auth: true,
    },
]);

const copyText = async (text: string, kind: 'token' | 'env') => {
    try {
        await navigator.clipboard.writeText(text);
        copied.value = kind;
        setTimeout(() => { copied.value = null; }, 2500);
    } catch {
        // ignore
    }
};

const generateToken = () => {
    if (!confirm('নতুন token তৈরি করলে পুরনো token বাতিল হবে। চালিয়ে যাবেন?')) return;
    generating.value = true;
    router.post(route('admin.api-documentation.generate-token'), {}, {
        preserveScroll: true,
        onFinish: () => { generating.value = false; },
    });
};

const revokeToken = () => {
    if (!confirm('বর্তমান MisLoan token বাতিল করতে চান?')) return;
    revoking.value = true;
    router.post(route('admin.api-documentation.revoke-token'), {}, {
        preserveScroll: true,
        onFinish: () => { revoking.value = false; },
    });
};

const testConnection = () => {
    testing.value = true;
    router.post(route('admin.api-documentation.test-connection'), {}, {
        preserveScroll: true,
        onFinish: () => { testing.value = false; },
    });
};

const clearTokenDisplay = () => {
    router.post(route('admin.api-documentation.clear-token-display'), {}, { preserveScroll: true });
};
</script>

<style scoped>
.btn-primary {
    @apply inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:from-indigo-700 hover:to-blue-800 transform hover:scale-105 transition-all duration-200 focus:ring-4 focus:ring-indigo-500/30;
}
</style>
