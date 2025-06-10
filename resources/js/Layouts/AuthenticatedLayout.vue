<script setup>
import { ref, onMounted, provide } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NotificationDropdown from '@/Components/NotificationDropdown.vue';
import LoadingIndicator from '@/Components/LoadingIndicator.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import MessageNotification from '@/Components/MessageNotification.vue';
import axios from 'axios';

const showingNavigationDropdown = ref(false);
const loadingIndicator = ref(null);
const toastNotification = ref(null);

const props = defineProps({
    breadcrumbItems: {
        type: Array,
        default: () => [],
    },
});

const showToast = (message, type = 'info', duration = 5000) => {
    toastNotification.value?.addToast(message, type, duration);
};
const showGlobalLoading = (message = 'Cargando...') => {
    loadingIndicator.value?.showLoading(message);
};
const hideGlobalLoading = () => {
    loadingIndicator.value?.hideLoading();
};

provide('showToast', showToast);
provide('showGlobalLoading', showGlobalLoading);
provide('hideGlobalLoading', hideGlobalLoading);

onMounted(() => {
    axios.interceptors.request.use(
        (config) => {
            if (!config.headers['X-Silent-Request']) {
                showGlobalLoading('Cargando...');
            }
            return config;
        },
        (error) => {
            hideGlobalLoading();
            return Promise.reject(error);
        }
    );
    axios.interceptors.response.use(
        (response) => {
            if (!response.config.headers['X-Silent-Request']) {
                hideGlobalLoading();
            }
            return response;
        },
        (error) => {
            hideGlobalLoading();
            return Promise.reject(error);
        }
    );
    const page = usePage();
    if (page.props.flash) {
        if (page.props.flash.success) showToast(page.props.flash.success, 'success');
        if (page.props.flash.error) showToast(page.props.flash.error, 'error');
    }
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-[#FAF6F0] dark:bg-gray-900">
            <nav class="border-b border-[#E0D5C7] bg-white dark:bg-gray-800 dark:border-gray-700">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('home')">
                                    <ApplicationLogo />
                                </Link>
                            </div>
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex sm:items-center">
                                <NavLink :href="route('items.my')" :active="route().current('items.my')">Todo Tiro</NavLink>
                                <NavLink :href="route('items.index')" :active="route().current('items.index')">Todo Cojo</NavLink>
                                <NavLink :href="route('requests.my')" :active="route().current('requests.my')">Todo Pido</NavLink>
                                <NavLink :href="route('requests.index')" :active="route().current('requests.index')">Comunidad</NavLink>
                                <div class="relative">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <button type="button" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out" :class="{ 'border-green-500 text-green-600 dark:border-green-400 dark:text-green-400 font-semibold': route().current('interests.*'), 'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none': !route().current('interests.*') }">
                                                Intereses
                                                <svg class="-me-1 ms-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            </button>
                                        </template>
                                        <template #content>
                                            <DropdownLink :href="route('interests.index')">Mis Intereses</DropdownLink>
                                            <DropdownLink :href="route('interests.received')">Quién lo Quiere</DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>
                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <DarkModeToggle class="mr-3" />
                            <NotificationDropdown />
                            <MessageNotification class="mr-3" />
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center rounded-md border border-transparent bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium leading-4 text-gray-600 dark:text-gray-300 transition duration-150 ease-in-out hover:text-gray-800 dark:hover:text-gray-100 focus:outline-none">
                                                {{ $page.props.auth.user.name }}
                                                <svg class="-me-0.5 ms-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            </button>
                                        </span>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">Perfil</DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">Cerrar Sesión</DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:focus:bg-gray-700">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /><path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('items.my')" :active="route().current('items.my')">Todo Tiro</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('items.index')" :active="route().current('items.index')">Todo Cojo</ResponsiveNavLink>
                    </div>
                </div>
            </nav>
            <header class="bg-white shadow-sm dark:bg-gray-800 dark:text-white border-b border-[#E0D5C7]" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                    <Breadcrumb v-if="breadcrumbItems && breadcrumbItems.length" :items="breadcrumbItems" class="mt-2" />
                </div>
            </header>
            <main><slot /></main>
        </div>
        <LoadingIndicator ref="loadingIndicator" />
        <ToastNotification ref="toastNotification" />
    </div>
</template>
