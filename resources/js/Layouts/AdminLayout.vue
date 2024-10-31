<template>
    <div class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-700">
        <!-- Sidebar -->
        <aside :class="[
            'bg-white dark:bg-gray-800 fixed inset-y-0 left-0 w-64 shadow-lg transition-transform transform lg:translate-x-0 z-50 md:z-10 overflow-y-auto',
            showSidebar ? 'translate-x-0' : '-translate-x-64'
        ]">
            <div class="p-4 flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">Mousumi NGO</h1>
                <button @click="toggleSidebar" class="lg:hidden text-gray-500 dark:text-gray-100 hover:text-gray-600">
                    <!-- Close Icon -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <nav class="mt-5">
                <ul>
                    <li v-for="item in navItems" :key="item.name" class="relative">
                        <Link v-if="!item.children" :href="item.link"
                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-50 hover:bg-gray-200 dark:hover:bg-gray-900"
                            :class="{ 'bg-gray-200 dark:bg-gray-900': isActive(item.link) }">
                        <svg v-if="item.icon" class="w-5 h-5 text-gray-600 dark:text-gray-50 mr-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path :d="item.icon"></path>
                        </svg>
                        <span>{{ item.name }}</span>
                        </Link>

                        <button v-else @click="toggleDropdown(item)"
                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-50 hover:bg-gray-200 dark:hover:bg-gray-800"
                            :class="{ 'bg-gray-200 dark:bg-gray-900': isActive(item.link) || hasActiveChild(item) }">
                            <svg v-if="item.icon" class="w-5 h-5 text-gray-600 dark:text-gray-50 mr-2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path :d="item.icon"></path>
                            </svg>
                            <span>{{ item.name }}</span>
                            <svg :class="{ 'transform rotate-180': item.isOpen || hasActiveChild(item) }"
                                class="w-4 h-4 transition-transform" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <ul v-if="item.children && (item.isOpen || hasActiveChild(item))" class="pl-8">
                            <li v-for="child in item.children" :key="child.name">
                                <Link :href="child.link"
                                    class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-50 hover:bg-gray-200 dark:hover:bg-gray-900 w-full rounded-md"
                                    :class="{ 'bg-gray-200 dark:bg-gray-900': isActive(child.link) }">
                                <svg v-if="child.icon" class="w-5 h-5 text-gray-600 dark:text-gray-50 mr-2" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                                </svg>
                                <span>{{ child.name }}</span>
                                </Link>
                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- Manual Links for Profile and Logout with Icons -->
                <div class="mt-5">
                    <Link href="/profile"
                        class="block flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900">
                    <svg class="w-5 h-5 text-gray-700 dark:text-white" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.31 0-10 1.67-10 5v2h20v-2c0-3.33-6.69-5-10-5z">
                        </path>
                    </svg>
                    <span>Profile</span>
                    </Link>

                    <Link :href="route('logout')" method="post" as="button"
                        class="block flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900">
                    <svg class="w-5 h-5 text-gray-700 dark:text-white" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 12H4m8-8l-4 4m4-4l4 4m0 8l-4-4m4 4l4-4"></path>
                    </svg>
                    <span>Logout</span>
                    </Link>
                </div>
            </nav>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <a href="/mousumi-ngo-app.apk" download
                    class="block flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Download App</span>
                </a>
            </div>

        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <header class="fixed top-0 left-0 right-0 lg:left-64 shadow-md z-20 flex justify-between items-center p-4">
                <button @click="toggleSidebar"
                    class="lg:hidden text-gray-500 dark:text-white hover:text-gray-600 dark:hover:text-gray-50">
                    <!-- Menu Icon -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="relative">
                    <button @click="toggleUserMenu" class="flex items-center space-x-2 focus:outline-none">
                        <img v-if="user.photo" :src="'/storage/' + user.photo" alt="Profile Photo"
                            class="w-8 h-8 rounded-full" />
                        <span class="text-gray-600 dark:text-gray-50">{{ user.name }}</span>
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-50" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div v-if="showUserMenu"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-600 border rounded-lg shadow-lg">
                        <Link href="/profile"
                            class="block px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900">
                        Profile</Link>
                        <Link :href="route('logout')" method="post" as="button"
                            class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900">
                        Logout</Link>
                    </div>
                </div>

                <div>
                    <button @click="switchTheme">
                        <i class="fa-solid fa-circle-half-stroke"></i>
                    </button>
                </div>
            </header>

            <main class="flex-1 p-6 mt-16 overflow-y-auto h-[calc(100vh-4rem)]">



                <div class="mx-auto">
                    <div v-if="showFlashSuccess"
                        class="mb-4 p-4 bg-green-100 text-green-700 rounded border-l-4 border-green-500 transition-transform transform duration-300 ease-in-out">
                        {{ flash.success }}
                    </div>
                    <div v-if="showFlashError"
                        class="mb-4 p-4 bg-red-100 text-red-700 rounded border-l-4 border-red-500 transition-transform transform duration-300 ease-in-out">
                        {{ flash.error }}
                    </div>
                    <LoadingProgress />
                    <slot></slot>
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
const navItems = ref([
    {
        name: 'Home',
        link: '/customer-search',
        icon: 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z',
        isActive: false,
    },
    {
        name: 'Members',
        link: '/admin/customers',
        icon: 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z',
        isActive: false,
    },
    ...(user.name === 'Super Admin' ? [
        {
            name: 'Branches',
            link: '/admin/branches',
            icon: 'M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819',
            isActive: false,
        },
        {
            name: 'Users',
            link: '/admin/users',
            icon: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
            isActive: false,
        },
    ] : []),
]);

const toggleDropdown = (item) => {
    item.isOpen = !item.isOpen;
};

// Utility methods
const isActive = (link) => {
    return usePage().url === link;
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
    // Handle logout logic here
};
</script>
