<template>
    <div
        class="relative flex h-screen overflow-hidden bg-gradient-to-b from-slate-100 via-white to-slate-100 text-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 dark:text-slate-100"
    >
        <div
            class="pointer-events-none absolute inset-0 z-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(59,130,246,0.1),transparent)] dark:bg-[radial-gradient(ellipse_70%_45%_at_50%_-15%,rgba(99,102,241,0.14),transparent)]"
            aria-hidden="true"
        />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 overflow-y-auto border-r border-slate-200/80 bg-white/90 shadow-xl shadow-slate-900/5 backdrop-blur-md transition-transform dark:border-slate-700/60 dark:bg-slate-900/90 dark:shadow-black/20',
                showSidebar ? 'translate-x-0' : '-translate-x-64 lg:translate-x-0',
            ]"
        >
            <div class="flex items-center justify-between border-b border-slate-200/60 p-4 dark:border-slate-700/50">
                <Link
                    href="/"
                    class="block min-w-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                >
                    <p
                        class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500"
                    >
                        Organization
                    </p>
                    <h1
                        class="truncate text-lg font-bold tracking-tight text-slate-900 transition-colors hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400"
                    >
                        Mousumi NGO
                    </h1>
                </Link>
                <button
                    type="button"
                    class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 lg:hidden dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                    aria-label="Close menu"
                    @click="toggleSidebar"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
            <nav class="mt-2 px-2 pb-6">
                <ul class="space-y-0.5">
                    <li v-for="item in navItems" :key="item.name" class="relative">
                        <Link
                            v-if="!item.children"
                            :href="item.link"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition dark:text-slate-300"
                            :class="
                                isActive(item.link)
                                    ? 'bg-indigo-50 text-indigo-900 ring-1 ring-indigo-200/70 dark:bg-indigo-950/50 dark:text-indigo-100 dark:ring-indigo-800/50'
                                    : 'hover:bg-slate-100 dark:hover:bg-slate-800/80'
                            "
                        >
                            <svg
                                v-if="item.icon"
                                class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400"
                                :class="isActive(item.link) ? 'text-indigo-600 dark:text-indigo-400' : ''"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path :d="item.icon" />
                            </svg>
                            <span>{{ item.name }}</span>
                        </Link>

                        <button
                            v-else
                            type="button"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-slate-600 transition dark:text-slate-300"
                            :class="
                                hasActiveChild(item)
                                    ? 'bg-indigo-50 text-indigo-900 ring-1 ring-indigo-200/70 dark:bg-indigo-950/50 dark:text-indigo-100 dark:ring-indigo-800/50'
                                    : 'hover:bg-slate-100 dark:hover:bg-slate-800/80'
                            "
                            @click="toggleDropdown(item)"
                        >
                            <svg
                                v-if="item.icon"
                                class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path :d="item.icon" />
                            </svg>
                            <span class="flex-1">{{ item.name }}</span>
                            <svg
                                class="h-4 w-4 shrink-0 transition-transform"
                                :class="{ 'rotate-180': item.isOpen || hasActiveChild(item) }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <ul
                            v-if="item.children && (item.isOpen || hasActiveChild(item))"
                            class="mt-0.5 space-y-0.5 border-l border-slate-200/80 py-1 pl-3 dark:border-slate-700/80"
                        >
                            <li v-for="child in item.children" :key="child.name">
                                <Link
                                    :href="child.link"
                                    class="flex items-center gap-2 rounded-lg py-2 pl-3 pr-2 text-sm text-slate-600 transition dark:text-slate-300"
                                    :class="
                                        isActive(child.link)
                                            ? 'bg-indigo-50/90 font-medium text-indigo-900 dark:bg-indigo-950/40 dark:text-indigo-100'
                                            : 'hover:bg-slate-100 dark:hover:bg-slate-800/60'
                                    "
                                >
                                    <svg
                                        v-if="child.icon"
                                        class="h-4 w-4 shrink-0 text-slate-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                        />
                                    </svg>
                                    <span>{{ child.name }}</span>
                                </Link>
                            </li>
                        </ul>
                    </li>
                </ul>

                <div class="mt-6 space-y-0.5 border-t border-slate-200/60 pt-4 dark:border-slate-700/50">
                    <Link
                        href="/profile"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800/80"
                    >
                        <svg
                            class="h-5 w-5 text-slate-500 dark:text-slate-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.31 0-10 1.67-10 5v2h20v-2c0-3.33-6.69-5-10-5z"
                            />
                        </svg>
                        <span>Profile</span>
                    </Link>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-red-700/90 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30"
                    >
                        <svg
                            class="h-5 w-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M16 12H4m8-8l-4 4m4-4l4 4m0 8l-4-4m4 4l4-4" />
                        </svg>
                        <span>Logout</span>
                    </Link>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="relative z-10 flex min-w-0 flex-1 flex-col lg:ml-64">
            <header
                class="fixed left-0 right-0 top-0 z-20 flex items-center justify-between gap-2 border-b border-slate-200/70 bg-white/80 px-3 py-2.5 shadow-sm backdrop-blur-md dark:border-slate-700/60 dark:bg-slate-900/80 sm:px-4 sm:py-3 lg:left-64"
            >
                <div class="flex min-w-0 flex-1 items-center gap-2">
                    <button
                        type="button"
                        class="-ml-1 shrink-0 rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 lg:hidden dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                        aria-label="Open menu"
                        @click="toggleSidebar"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                    <Link
                        href="/"
                        class="inline-flex min-w-0 max-w-[calc(100vw-8.5rem)] items-center justify-center gap-2 rounded-full border border-slate-200/80 bg-white/90 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-600 shadow-sm backdrop-blur-sm transition hover:border-slate-300 hover:bg-white hover:text-slate-900 dark:border-slate-600/80 dark:bg-slate-800/90 dark:text-slate-300 dark:hover:border-slate-500 dark:hover:bg-slate-800 dark:hover:text-white sm:max-w-none sm:px-4"
                    >
                        <svg
                            class="h-3.5 w-3.5 shrink-0 opacity-80"
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
                        <span class="truncate">Apps</span>
                    </Link>
                </div>
                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <div class="relative">
                        <button
                            type="button"
                            class="flex max-w-[9rem] items-center gap-2 rounded-full py-1 pl-1 pr-2 text-left transition hover:bg-slate-100 dark:hover:bg-slate-800/80 sm:max-w-none sm:gap-2 sm:pr-3"
                            @click="toggleUserMenu"
                        >
                            <img
                                v-if="user.photo"
                                :src="'/storage/' + user.photo"
                                alt=""
                                class="h-8 w-8 shrink-0 rounded-full ring-2 ring-white dark:ring-slate-700"
                            />
                            <span
                                class="truncate text-sm font-medium text-slate-700 dark:text-slate-200"
                                >{{ user.name }}</span
                            >
                            <svg
                                class="h-4 w-4 shrink-0 text-slate-500 dark:text-slate-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>
                        <div
                            v-if="showUserMenu"
                            class="absolute right-0 z-30 mt-2 w-48 overflow-hidden rounded-xl border border-slate-200/80 bg-white/95 py-1 shadow-xl shadow-slate-900/10 backdrop-blur-md dark:border-slate-600/80 dark:bg-slate-800/95 dark:shadow-black/30"
                        >
                            <Link
                                href="/profile"
                                class="block px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/50"
                            >
                                Profile
                            </Link>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="w-full px-4 py-2.5 text-left text-sm font-medium text-red-700 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30"
                            >
                                Logout
                            </Link>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200/80 bg-white/90 text-slate-500 shadow-sm backdrop-blur-sm transition hover:border-slate-300 hover:text-slate-800 dark:border-slate-600/80 dark:bg-slate-800/90 dark:text-slate-400 dark:hover:border-slate-500 dark:hover:text-white"
                        aria-label="Toggle color theme"
                        @click="switchTheme"
                    >
                        <i class="fa-solid fa-circle-half-stroke text-sm" />
                    </button>
                </div>
            </header>

            <main
                class="mt-16 h-[calc(100vh-4rem)] flex-1 overflow-y-auto bg-slate-50/40 p-6 dark:bg-slate-950/40"
            >
                <div class="mx-auto">
                    <div
                        v-if="showFlashSuccess"
                        class="mb-4 rounded-xl border border-emerald-200/80 bg-emerald-50/95 px-4 py-3 text-sm font-medium text-emerald-900 shadow-sm dark:border-emerald-800/50 dark:bg-emerald-950/40 dark:text-emerald-100"
                    >
                        {{ flash.success }}
                    </div>
                    <div
                        v-if="showFlashError"
                        class="mb-4 rounded-xl border border-red-200/80 bg-red-50/95 px-4 py-3 text-sm font-medium text-red-900 shadow-sm dark:border-red-800/50 dark:bg-red-950/40 dark:text-red-100"
                    >
                        {{ flash.error }}
                    </div>
                    <LoadingProgress />
                    <slot />
                </div>
            </main>
        </div>
        <ScrollToTopButton />
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { switchTheme } from '@/theme';
import LoadingProgress from '@/Components/LoadingProgress.vue';
import ScrollToTopButton from '@/Components/ScrollToTopButton.vue';

const { props } = usePage();
const flash = props.flash;
const showSidebar = ref(false);
const showUserMenu = ref(false);
const showFlashSuccess = ref(!!flash.success);
const showFlashError = ref(!!flash.error);

onMounted(() => {
    if (flash.success || flash.error) {
        setTimeout(() => {
            showFlashSuccess.value = false;
            showFlashError.value = false;
        }, 2000);
    }
});

watch(() => flash.success, (newVal) => {
    if (newVal) {
        showFlashSuccess.value = true;
        setTimeout(() => showFlashSuccess.value = false, 2000);
    }
});

watch(() => flash.error, (newVal) => {
    if (newVal) {
        showFlashError.value = true;
        setTimeout(() => showFlashError.value = false, 2000);
    }
});

const toggleSidebar = () => {
    showSidebar.value = !showSidebar.value;
};

const toggleUserMenu = () => {
    showUserMenu.value = !showUserMenu.value;
};

// Navigation items with expanded states
const user = props.auth.user;
const isSuperAdmin = user.name === 'Super Admin';

const navItems = ref([
    {
        name: 'Block Register',
        icon: 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285zm0 13.036h.008v.008H9v-.008z',
        isOpen: false,
        children: [
            {
                name: 'Search Member',
                link: '/customer-search',
                icon: 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z',
            },
            {
                name: 'Member',
                link: '/admin/customers',
                icon: 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z',
            },
            {
                name: 'Reports',
                link: '/admin/reports',
                icon: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z',
            },
        ],
    },
    {
        name: 'Receipt Inventory',
        icon: 'M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3',
        isOpen: false,
        children: [
            {
                name: 'Receipts Inventory',
                link: '/payment-receipts',
                icon: 'M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3',
            },
            {
                name: 'Search Receipt',
                link: '/payment-receipts/search',
                icon: 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z',
            },
            ...(isSuperAdmin
                ? [
                      {
                          name: 'Receipt Payment Reports',
                          link: '/admin/receipt-transaction-report',
                          icon: 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
                      },
                  ]
                : []),
        ],
    },
    ...(isSuperAdmin
        ? [
              {
                  name: 'Administration',
                  icon: 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
                  isOpen: false,
                  children: [
                      {
                          name: 'Branches',
                          link: '/admin/branches',
                          icon: 'M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819',
                      },
                      {
                          name: 'Users',
                          link: '/admin/users',
                          icon: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
                      },
                      /* Voluntary Savings — temporarily hidden
                      {
                          name: 'Voluntary Savings',
                          link: '/admin/voluntary-savings',
                          icon: 'M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9',
                      },
                      */
                  ],
              },
          ]
        : []),
]);

const toggleDropdown = (item) => {
    item.isOpen = !item.isOpen;
};

const collectNavLinks = (items) => {
    const links = [];
    for (const item of items) {
        if (item.link) {
            links.push(item.link);
        }
        if (Array.isArray(item.children)) {
            links.push(...collectNavLinks(item.children));
        }
    }
    return links;
};

// Utility methods — longest matching nav link wins (avoids parent + child both active)
const isActive = (link) => {
    const url = usePage().url || '/';
    const path = url.split('?')[0] || '/';
    if (link === '/') {
        return path === '/' || path === '';
    }

    const allLinks = collectNavLinks(navItems.value);
    const matchingLinks = allLinks.filter(
        (l) => l === path || (l !== '/' && path.startsWith(`${l}/`)),
    );

    if (matchingLinks.length > 0) {
        const bestMatch = matchingLinks.sort((a, b) => b.length - a.length)[0];
        return bestMatch === link;
    }

    return path === link || path.startsWith(`${link}/`);
};

const hasActiveChild = (parent) => {
    if (!Array.isArray(parent.children)) {
        return false; // No children to check
    }
    return parent.children.some(child =>
        isActive(child.link) || (Array.isArray(child.children) && hasActiveChild(child))
    );
};

const logout = () => {

};
</script>
