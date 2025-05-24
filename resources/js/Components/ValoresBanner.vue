<!-- resources/js/Components/ValoresBanner.vue -->
<template>
    <div class="bg-gradient-to-r from-[#00913F] to-[#007833] text-white py-12 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Todo Cojo, Todo Tiro</h2>
                <p class="text-xl opacity-90">Juntos construimos una economía circular y sostenible</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-10">
                <!-- Sostenibilidad -->
                <div class="text-center">
                    <div class="bg-white/20 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Sostenibilidad</h3>
                    <p class="opacity-85">Reducimos residuos dando nueva vida a los objetos</p>
                </div>

                <!-- Comunidad -->
                <div class="text-center">
                    <div class="bg-white/20 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Comunidad</h3>
                    <p class="opacity-85">Conectamos personas para ayudarse mutuamente</p>
                </div>

                <!-- Circularidad -->
                <div class="text-center">
                    <div class="bg-white/20 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Economía Circular</h3>
                    <p class="opacity-85">Fomentamos el ciclo continuo de reutilización</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// Este componente puede recibir props si necesitas personalizarlo
</script>
