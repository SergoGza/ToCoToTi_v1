<!-- resources/js/Layouts/AuthenticatedLayout.vue -->
<template>
    <div>
        <!-- Debugger para probar el modo oscuro -->
        <div v-if="false" class="bg-white dark:bg-black p-4 dark:text-white text-center text-xl">
            TEMA ACTUAL: {{ isDarkMode ? 'OSCURO' : 'CLARO' }}
            <button @click="window.toggleDarkMode()" class="ml-4 bg-blue-500 p-2 rounded text-white">
                Cambiar tema manualmente
            </button>
        </div>

        <div class="min-h-screen bg-[#FAF6F0] dark:bg-gray-900">
            <nav
                class="border-b border-[#E0D5C7] bg-white dark:bg-gray-800 dark:border-gray-700"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('home')">
                                    <ApplicationLogo/>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex sm:items-center">
                                <NavLink
                                    :href="route('items.my')"
                                    :active="route().current('items.my')"
                                    class="nav-link"
                                >
                                    Todo Tiro
                                </NavLink>

                                <NavLink
                                    :href="route('items.index')"
                                    :active="route().current('items.index')"
                                    class="nav-link"
                                >
                                    Todo Cojo
                                </NavLink>

                                <NavLink
                                    :href="route('requests.my')"
                                    :active="route().current('requests.my')"
                                    class="nav-link"
                                >
                                    Todo Pido
                                </NavLink>

                                <NavLink
                                    :href="route('requests.index')"
                                    :active="route().current('requests.index')"
                                    class="nav-link"
                                >
                                    Comunidad
                                </NavLink>

                                <!-- Dropdown de Intereses - ALINEACIÓN CORREGIDA -->
                                <div class="relative">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                                                :class="{
                        'border-[#00913F] text-[#00913F] font-semibold': route().current('interests.*'),
                        'border-transparent text-[#333333] hover:text-[#00913F] hover:border-[#00913F]/30 focus:outline-none focus:text-[#00913F] focus:border-[#00913F]/30': !route().current('interests.*')
                    }"
                                            >
                                                Intereses
                                                <svg
                                                    class="-me-1 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </template>

                                        <template #content>
                                            <DropdownLink :href="route('interests.index')">
                                                Mis Intereses
                                            </DropdownLink>
                                            <DropdownLink :href="route('interests.received')">
                                                Quién lo Quiere
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">

                            <!-- Dark Mode Toggle -->
                            <DarkModeToggle class="mr-3"/>

                            <!-- Notificaciones Dropdown -->
                            <NotificationDropdown/>

                            <!-- Notificación de Mensajes -->
                            <MessageNotification class="mr-3"/>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium leading-4 text-gray-500 dark:text-gray-300 transition duration-150 ease-in-out hover:text-gray-700 dark:hover:text-white focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:focus:bg-gray-700"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('items.my')"
                            :active="route().current('items.my')"
                        >
                            Todo Tiro
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('items.index')"
                            :active="route().current('items.index')"
                        >
                            Todo Cojo
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('requests.my')"
                            :active="route().current('requests.my')"
                        >
                            Todo Pido
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('requests.index')"
                            :active="route().current('requests.index')"
                        >
                            Comunidad
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('interests.index')"
                            :active="route().current('interests.index')"
                        >
                            Mis Intereses
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('interests.received')"
                            :active="route().current('interests.received')"
                        >
                            Quién lo Quiere
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('notifications.index')"
                            :active="route().current('notifications.index')"
                        >
                            Notificaciones
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 dark:border-gray-700 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800 dark:text-gray-200"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow-sm dark:bg-gray-800 dark:text-white border-b border-[#E0D5C7]"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header"/>

                    <!-- Breadcrumb (Solo se muestra si se proporcionan breadcrumbItems) -->
                    <Breadcrumb v-if="breadcrumbItems && breadcrumbItems.length" :items="breadcrumbItems" class="mt-2"/>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot/>
            </main>
        </div>

        <!-- Componentes de UI -->
        <LoadingIndicator ref="loadingIndicator"/>
        <ToastNotification ref="toastNotification"/>
    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
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
import {Link, usePage} from '@inertiajs/vue3';
import axios from 'axios';
import MessageNotification from '@/Components/MessageNotification.vue';


const showingNavigationDropdown = ref(false);

const loadingIndicator = ref(null);
const toastNotification = ref(null);
const isDarkMode = ref(false);

// Propiedad para breadcrumbs
const props = defineProps({
    breadcrumbItems: {
        type: Array,
        default: () => []
    }
});

const showToast = (message, type = 'info', duration = 5000) => {
    if (toastNotification.value) {
        toastNotification.value.addToast(message, type, duration);
    } else {
        window.dispatchEvent(
            new CustomEvent('add-toast', {
                detail: {message, type, duration}
            })
        );
    }
};

const showGlobalLoading = (message = 'Cargando...') => {
    if (loadingIndicator.value) {
        loadingIndicator.value.showLoading(message);
    } else {
        window.dispatchEvent(
            new CustomEvent('show-loading', {
                detail: {message}
            })
        );
    }
};

const hideGlobalLoading = () => {
    if (loadingIndicator.value) {
        loadingIndicator.value.hideLoading();
    } else {
        window.dispatchEvent(new CustomEvent('hide-loading'));
    }
};

// Exponer las funciones globalmente
window.showToast = showToast;
window.showGlobalLoading = showGlobalLoading;
window.hideGlobalLoading = hideGlobalLoading;

onMounted(() => {
    // Detectar el modo oscuro actual
    isDarkMode.value = document.documentElement.classList.contains('dark');

    // Configurar interceptors de Axios
    axios.interceptors.request.use(
        (config) => {
            // Si la petición no tiene un header personalizado para evitar el loader
            if (!config.headers['X-No-Loading'] && !config.headers['X-Silent-Request']) {
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
            hideGlobalLoading();
            return response;
        },
        (error) => {
            hideGlobalLoading();
            return Promise.reject(error);
        }
    );


    // Añadir escucha para flash messages de Inertia
    const page = usePage();
    if (page.props.flash) {
        if (page.props.flash.success && toastNotification.value) {
            toastNotification.value.success(page.props.flash.success);
        }
        if (page.props.flash.error && toastNotification.value) {
            toastNotification.value.error(page.props.flash.error);
        }
    }
});
</script>
