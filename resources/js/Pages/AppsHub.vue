<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { switchTheme } from '@/theme';

const page = usePage();
const user = computed(() => page.props.auth.user);

const apps = [
    {
        key: 'block',
        title: 'Block Register',
        href: () => route('customer.search'),
        external: false,
        gradient:
            'from-slate-700 via-slate-800 to-slate-900 shadow-slate-900/25 dark:shadow-black/40',
        iconPath:
            'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285zm0 13.036h.008v.008H9v-.008z',
    },
    {
        key: 'mis',
        title: 'MIS Loan',
        href: () => 'https://mis.mousumibd.org/',
        external: true,
        gradient:
            'from-amber-500 via-orange-500 to-orange-600 shadow-orange-600/30 dark:shadow-orange-900/40',
        iconPath:
            'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m0-12h-9',
    },
    {
        key: 'pos',
        title: 'POS',
        href: () => 'https://pos.mousumibd.org/',
        external: true,
        gradient:
            'from-emerald-500 via-teal-500 to-teal-700 shadow-teal-700/25 dark:shadow-teal-950/40',
        iconPath:
            'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z',
    },
    {
        key: 'hrm',
        title: 'HR Management',
        href: () => 'https://hrm.mousumibd.org/',
        external: true,
        gradient:
            'from-indigo-500 via-violet-600 to-violet-800 shadow-violet-800/25 dark:shadow-violet-950/40',
        iconPath:
            'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
    },
];
</script>

<template>
    <Head title="Apps" />

    <div
        class="relative min-h-screen overflow-x-hidden bg-gradient-to-b from-slate-100 via-white to-slate-100 text-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 dark:text-slate-100"
    >
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(59,130,246,0.12),transparent)] dark:bg-[radial-gradient(ellipse_70%_45%_at_50%_-15%,rgba(99,102,241,0.18),transparent)]"
            aria-hidden="true"
        />

        <div
            class="relative z-10 flex min-h-screen flex-col items-center justify-center px-5 pb-16 pt-20 sm:px-8 sm:pb-20 sm:pt-24"
        >
            <div
                class="absolute right-4 top-4 flex items-center gap-2 sm:right-6 sm:top-6 sm:gap-3"
            >
                <Link
                    v-if="user"
                    href="/customer-search"
                    class="inline-flex rounded-full border border-slate-200/80 bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-600 shadow-sm backdrop-blur-sm transition hover:border-slate-300 hover:bg-white hover:text-slate-900 dark:border-slate-600/80 dark:bg-slate-800/80 dark:text-slate-300 dark:hover:border-slate-500 dark:hover:bg-slate-800 dark:hover:text-white"
                >
                    Workspace
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

            <p
                class="mb-2 text-[10px] font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500"
            >
                Mousumi NGO
            </p>
            <h1 class="mb-1 text-center text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                Applications
            </h1>
            <p
                class="mb-12 max-w-md text-center text-sm leading-relaxed text-slate-500 dark:text-slate-400 sm:mb-16 sm:text-base"
            >
                Choose an app to continue. All links open in this window.
            </p>

            <ul
                class="mx-auto grid max-w-3xl grid-cols-2 gap-x-6 gap-y-10 sm:max-w-4xl sm:gap-x-10 sm:gap-y-12 md:grid-cols-4 md:gap-x-8"
                role="list"
            >
                <li v-for="app in apps" :key="app.key" class="flex list-none justify-center">
                    <component
                        :is="app.external ? 'a' : Link"
                        :href="app.href()"
                        class="group flex w-full max-w-[9.5rem] flex-col items-center sm:max-w-[10.5rem]"
                    >
                        <div
                            class="relative flex aspect-square w-full max-w-[7.5rem] items-center justify-center rounded-[1.75rem] bg-gradient-to-br shadow-xl ring-1 ring-black/[0.04] transition duration-300 ease-out will-change-transform sm:max-w-[8.5rem] sm:rounded-[2rem] md:max-w-[9rem] dark:ring-white/[0.06] group-hover:-translate-y-1 group-hover:shadow-2xl"
                            :class="app.gradient"
                        >
                            <svg
                                class="h-[42%] w-[42%] text-white drop-shadow-sm sm:h-[44%] sm:w-[44%]"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.35"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    :d="app.iconPath"
                                />
                            </svg>
                        </div>
                        <span
                            class="mt-4 text-center text-[13px] font-semibold leading-snug tracking-tight text-slate-800 dark:text-slate-100 sm:text-sm"
                        >
                            {{ app.title }}
                        </span>
                    </component>
                </li>
            </ul>
        </div>
    </div>
</template>
