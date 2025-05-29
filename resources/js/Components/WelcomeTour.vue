<!-- resources/js/Components/WelcomeTour.vue -->
<template>
    <div v-if="showTour" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#00913F] to-[#007833] text-white p-6 rounded-t-xl">
                <h2 class="text-2xl font-bold mb-2">¡Bienvenido a ToCoToTi! 🎉</h2>
                <p class="opacity-90">Te mostramos cómo empezar en 3 simples pasos</p>
            </div>

            <!-- Contenido -->
            <div class="p-6">
                <!-- Paso actual -->
                <div v-if="currentStep === 1" class="space-y-4">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-[#00913F]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#00913F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#333333]">Paso 1: Comparte lo que no uses</h3>
                    </div>
                    <p class="text-[#825028] text-center">
                        ¿Tienes objetos en buen estado que ya no usas? ¡Publícalos!
                        Alguien en la comunidad podría necesitarlos.
                    </p>
                    <div class="bg-[#FAF6F0] p-4 rounded-lg">
                        <p class="text-sm text-[#333333]">
                            <strong>Tip:</strong> Las fotos claras y descripciones detalladas
                            ayudan a que tus items se compartan más rápido.
                        </p>
                    </div>
                </div>

                <div v-if="currentStep === 2" class="space-y-4">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-[#825028]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#825028]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#333333]">Paso 2: Encuentra lo que necesitas</h3>
                    </div>
                    <p class="text-[#825028] text-center">
                        Explora los items disponibles o crea una solicitud para algo específico
                        que estés buscando. ¡La comunidad te ayudará!
                    </p>
                    <div class="bg-[#FAF6F0] p-4 rounded-lg">
                        <p class="text-sm text-[#333333]">
                            <strong>Tip:</strong> Usa los filtros por categoría y ubicación
                            para encontrar exactamente lo que buscas.
                        </p>
                    </div>
                </div>

                <div v-if="currentStep === 3" class="space-y-4">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-[#00913F]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#00913F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#333333]">Paso 3: Conecta con la comunidad</h3>
                    </div>
                    <p class="text-[#825028] text-center">
                        Cuando alguien muestre interés en tus items o tú en los de otros,
                        podrán comunicarse para coordinar la entrega. ¡Así de simple!
                    </p>
                    <div class="bg-[#FAF6F0] p-4 rounded-lg">
                        <p class="text-sm text-[#333333]">
                            <strong>Recuerda:</strong> "Todo Cojo, Todo Tiro" -
                            Cada intercambio ayuda a construir una economía más sostenible.
                        </p>
                    </div>
                </div>

                <!-- Indicadores de progreso -->
                <div class="flex justify-center space-x-2 mt-8">
                    <button
                        v-for="step in 3"
                        :key="step"
                        @click="currentStep = step"
                        class="w-2 h-2 rounded-full transition-all duration-200"
                        :class="currentStep === step ? 'bg-[#00913F] w-8' : 'bg-gray-300'"
                    />
                </div>

                <!-- Botones de navegación -->
                <div class="flex justify-between mt-8">
                    <button
                        v-if="currentStep > 1"
                        @click="currentStep--"
                        class="px-4 py-2 text-[#825028] hover:text-[#6b3f1f] font-semibold transition-colors duration-200"
                    >
                        ← Anterior
                    </button>
                    <div v-else></div>

                    <button
                        v-if="currentStep < 3"
                        @click="currentStep++"
                        class="px-6 py-2 bg-[#00913F] text-white rounded-lg hover:bg-[#007833] transition-colors duration-200"
                    >
                        Siguiente →
                    </button>
                    <button
                        v-else
                        @click="completeTour"
                        class="px-6 py-2 bg-[#00913F] text-white rounded-lg hover:bg-[#007833] transition-colors duration-200"
                    >
                        ¡Empezar!
                    </button>
                </div>
            </div>

            <!-- Botón de saltar -->
            <button
                @click="skipTour"
                class="absolute top-4 right-4 text-white/80 hover:text-white"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['complete', 'skip']);

const showTour = ref(props.show);
const currentStep = ref(1);

const completeTour = () => {
    // Guardar en localStorage que el tour fue completado
    localStorage.setItem('tocototi_tour_completed', 'true');
    emit('complete');
    showTour.value = false;
};

const skipTour = () => {
    localStorage.setItem('tocototi_tour_completed', 'true');
    emit('skip');
    showTour.value = false;
};
</script>
