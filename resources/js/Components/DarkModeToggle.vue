<!-- resources/js/Components/DarkModeToggle.vue -->
<template>
    <button
        @click="toggleTheme"
        class="p-2 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-600 transition-colors"
        :class="{
            'bg-gray-200 hover:bg-gray-300': !isDarkMode,
            'bg-gray-700 hover:bg-gray-600 text-white': isDarkMode
        }"
        :title="isDarkMode ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
    >
        <!-- Icono de sol (modo claro) -->
        <svg
            v-if="isDarkMode"
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-yellow-300"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
            />
        </svg>

        <!-- Icono de luna (modo oscuro) -->
        <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-gray-700"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
            />
        </svg>
    </button>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

// Estado local del modo oscuro
const isDarkMode = ref(false);

// Detectar el modo oscuro actual al montar el componente
onMounted(() => {
    // Comprobar si la clase 'dark' está presente en el elemento HTML
    isDarkMode.value = document.documentElement.classList.contains('dark');

    // Observar cambios en la clase del elemento HTML
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                isDarkMode.value = document.documentElement.classList.contains('dark');
            }
        });
    });

    observer.observe(document.documentElement, { attributes: true });

    return () => observer.disconnect();
});

const toggleTheme = () => {
    if (typeof window.toggleDarkMode === 'function') {
        window.toggleDarkMode();
    } else {
        console.error('La función toggleDarkMode no está disponible en el objeto window');

        isDarkMode.value = !isDarkMode.value;

        if (isDarkMode.value) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }
};
</script>
