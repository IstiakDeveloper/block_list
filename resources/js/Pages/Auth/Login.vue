<script setup>
import { ref, computed } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { switchTheme } from '@/theme';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    login: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const passwordFieldType = computed(() => (showPassword.value ? 'text' : 'password'));

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <Head title="Sign in" />

    <div
        class="relative min-h-screen overflow-x-hidden bg-gradient-to-b from-slate-100 via-white to-slate-100 text-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 dark:text-slate-100"
    >
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(59,130,246,0.12),transparent)] dark:bg-[radial-gradient(ellipse_70%_45%_at_50%_-15%,rgba(99,102,241,0.18),transparent)]"
            aria-hidden="true"
        />

        <div
            class="relative z-10 flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6"
        >
            <div
                class="absolute right-4 top-4 flex items-center gap-2 sm:right-6 sm:top-6 sm:gap-3"
            >
                <Link
                    href="/"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-600 shadow-sm backdrop-blur-sm transition hover:border-slate-300 hover:bg-white hover:text-slate-900 dark:border-slate-600/80 dark:bg-slate-800/80 dark:text-slate-300 dark:hover:border-slate-500 dark:hover:bg-slate-800 dark:hover:text-white"
                >
                    <svg
                        class="h-3.5 w-3.5 opacity-70"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"
                        />
                    </svg>
                    Apps
                </Link>
                <button
                    type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200/80 bg-white/80 text-slate-500 shadow-sm backdrop-blur-sm transition hover:border-slate-300 hover:text-slate-800 dark:border-slate-600/80 dark:bg-slate-800/80 dark:text-slate-400 dark:hover:border-slate-500 dark:hover:text-white"
                    aria-label="Toggle color theme"
                    @click="switchTheme"
                >
                    <i class="fa-solid fa-circle-half-stroke text-sm" />
                </button>
            </div>

            <div
                class="w-full max-w-[420px] rounded-[1.75rem] border border-slate-200/70 bg-white/85 p-8 shadow-2xl shadow-slate-900/5 ring-1 ring-black/[0.03] backdrop-blur-md dark:border-slate-700/60 dark:bg-slate-900/80 dark:shadow-black/30 dark:ring-white/[0.04] sm:p-10"
            >
                <p
                    class="mb-1 text-center text-[10px] font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500"
                >
                    Mousumi NGO
                </p>
                <h1
                    class="mb-1 text-center text-2xl font-bold tracking-tight text-slate-900 dark:text-white"
                >
                    Sign in
                </h1>
                <p class="mb-8 text-center text-sm text-slate-500 dark:text-slate-400">
                    Use your email or your 4-digit branch code, then your password.
                </p>

                <div
                    v-if="status"
                    class="mb-6 rounded-xl border border-emerald-200/80 bg-emerald-50/90 px-4 py-3 text-center text-sm font-medium text-emerald-800 dark:border-emerald-800/50 dark:bg-emerald-950/40 dark:text-emerald-200"
                >
                    {{ status }}
                </div>

                <form class="space-y-5" @submit.prevent="submit">
                    <div>
                        <label
                            for="login"
                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                        >
                            Email or 4-digit branch code
                        </label>
                        <input
                            id="login"
                            v-model="form.login"
                            type="text"
                            required
                            autofocus
                            autocomplete="username"
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-600 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/25"
                            placeholder="0000"
                        />
                        <InputError class="mt-2" :message="form.errors.login" />
                    </div>

                    <div>
                        <label
                            for="password"
                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                        >
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="passwordFieldType"
                                required
                                autocomplete="current-password"
                                class="block w-full rounded-xl border border-slate-200 bg-white py-3 pl-4 pr-12 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-600 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/25"
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                class="absolute right-1.5 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                                :aria-pressed="showPassword"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                @click="togglePasswordVisibility"
                            >
                                <i
                                    class="fa-solid text-base"
                                    :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"
                                    aria-hidden="true"
                                />
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <label class="flex cursor-pointer select-none items-center gap-2.5">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="text-sm font-medium text-slate-600 dark:text-slate-300"
                                >Remember me</span
                            >
                        </label>
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                        >
                            Forgot password?
                        </Link>
                    </div>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:from-indigo-500 hover:to-violet-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-slate-900"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Signing in…</span>
                        <span v-else>Sign in</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
