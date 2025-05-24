<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="auth-layout flex min-h-screen flex-col items-center bg-[#FAF6F0] pt-6 sm:justify-center sm:pt-0"
    >
        <!-- Contenedor del logo con más espacio y mejor presentación -->
        <div class="mb-8 text-center">
            <Link href="/" class="inline-block">
                <ApplicationLogo />
            </Link>
            <p class="text-[#825028] text-sm mt-4">Todo Cojo, Todo Tiro</p>
        </div>

        <!-- Tarjeta de contenido -->
        <div
            class="w-full overflow-hidden bg-white px-8 py-6 shadow-xl sm:max-w-md sm:rounded-xl border border-[#E0D5C7]"
        >
            <slot />
        </div>

        <!-- Footer opcional -->
        <div class="mt-8 text-center text-sm text-[#825028]">
            <p>&copy; {{ new Date().getFullYear() }} ToCoToTi. Construyendo comunidad.</p>
        </div>
    </div>
</template>

<style scoped>
/* Animación suave al cargar */
.auth-layout {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Sombra más pronunciada para la tarjeta */
.shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(130, 80, 40, 0.1),
    0 10px 10px -5px rgba(130, 80, 40, 0.04);
}
</style>
