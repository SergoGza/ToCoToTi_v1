<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registro - ToCoToTi" />

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-[#333333]">Únete a la comunidad</h2>
            <p class="text-[#825028] mt-2">Comienza a compartir y recibir</p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nombre completo" class="text-[#333333] font-semibold" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Juan Pérez"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Correo electrónico" class="text-[#333333] font-semibold" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="tu@email.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" class="text-[#333333] font-semibold" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar contraseña"
                    class="text-[#333333] font-semibold"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Repite tu contraseña"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-6">
                <PrimaryButton
                    class="w-full justify-center py-3 text-base"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Crear cuenta
                </PrimaryButton>
            </div>

            <div class="mt-6 text-center">
                <span class="text-sm text-[#333333]">
                    ¿Ya tienes cuenta?
                    <Link
                        :href="route('login')"
                        class="text-[#00913F] hover:text-[#007833] font-semibold transition-colors duration-200"
                    >
                        Inicia sesión aquí
                    </Link>
                </span>
            </div>
        </form>

        <!-- Beneficios de unirse -->
        <div class="mt-8 pt-6 border-t border-[#E0D5C7]">
            <p class="text-sm text-center text-[#825028] font-semibold mb-3">Al unirte podrás:</p>
            <ul class="text-sm text-[#333333] space-y-1">
                <li class="flex items-center">
                    <svg class="w-4 h-4 text-[#00913F] mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Compartir objetos que ya no uses
                </li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 text-[#00913F] mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Encontrar lo que necesitas gratis
                </li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 text-[#00913F] mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Contribuir a la sostenibilidad
                </li>
            </ul>
        </div>
    </GuestLayout>
</template>
