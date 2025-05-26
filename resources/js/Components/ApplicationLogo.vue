<template>
    <div class="logo-container">
        <!-- Intenta cargar el logo, si no existe muestra un placeholder -->
        <img
            :src="logoSrc"
            :alt="logoAlt"
            @error="handleImageError"
            class="logo-image"
        />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// Props para personalizar el logo
const props = defineProps({
    size: {
        type: String,
        default: 'normal', // 'small', 'normal', 'large'
    }
});

// Estado para manejar errores de carga
const imageError = ref(false);


const logoPath = [
    '/images/logo-tocototi.png',
   ];


const logoSrc = computed(() => {
    if (imageError.value) {
        // Si hay error, mostrar un placeholder con el texto
        return 'data:image/svg+xml;base64,' + btoa(`
            <svg width="200" height="60" xmlns="http://www.w3.org/2000/svg">
                <rect width="200" height="60" fill="#FAF6F0" rx="8"/>
                <text x="100" y="35" font-family="Open Sans, sans-serif" font-size="24" font-weight="bold" text-anchor="middle" fill="#00913F">ToCoToTi</text>
            </svg>
        `);
    }

    return logoPath[0];
});

const logoAlt = ref('ToCoToTi - Todo Cojo, Todo Tiro');

// Maneja el error si la imagen no se carga
const handleImageError = () => {
    console.warn('Logo no encontrado en:', logoPath[0]);
    imageError.value = true;
};
</script>

<style scoped>
.logo-container {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Tamaño normal (navegación) */
.logo-image {
    height: 50px;
    width: auto;
    object-fit: contain;
    transition: all 0.3s ease;
}

/* En la navegación principal */
nav .logo-image {
    height: 45px;
}

/* En páginas de autenticación (login, register, etc) */
.auth-layout .logo-image {
    height: 150px;
    margin-bottom: 1rem;
}

/* Efecto hover suave */
.logo-image:hover {
    transform: scale(1.05);
    filter: brightness(1.1);
}

/* Tamaños responsivos */
@media (max-width: 640px) {
    .logo-image {
        height: 40px;
    }

    nav .logo-image {
        height: 35px;
    }

    .auth-layout .logo-image {
        height: 120px;
    }
}

/* Para pantallas muy grandes */
@media (min-width: 1280px) {
    .logo-image {
        height: 60px;
    }

    nav .logo-image {
        height: 50px;
    }

    .auth-layout .logo-image {
        height: 180px;
    }
}
</style>
