<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar sesión - ToCoToTi"/>

        <div v-if="status" class="mb-4 text-sm font-medium text-[#00913F]">
            {{ status }}
        </div>

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-[#333333]">¡Bienvenido de vuelta!</h2>
            <p class="text-[#825028] mt-2">Inicia sesión para continuar compartiendo</p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Correo electrónico" class="text-[#333333] font-semibold"/>

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="tu@email.com"
                />

                <InputError class="mt-2" :message="form.errors.email"/>
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" class="text-[#333333] font-semibold"/>

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password"/>
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember"/>
                    <span class="ms-2 text-sm text-[#333333]">
                        Recordar mi sesión
                    </span>
                </label>
            </div>

            <div class="mt-6">
                <PrimaryButton
                    class="w-full justify-center py-3 text-base"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Iniciar sesión
                </PrimaryButton>
            </div>

            <div class="mt-6 text-center space-y-2">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-[#825028] hover:text-[#6b3f1f] transition-colors duration-200"
                >
                    ¿Olvidaste tu contraseña?
                </Link>

                <div class="text-sm text-[#333333]">
                    ¿No tienes cuenta?
                    <Link
                        :href="route('register')"
                        class="text-[#00913F] hover:text-[#007833] font-semibold transition-colors duration-200"
                    >
                        Regístrate aquí
                    </Link>
                </div>
            </div>
        </form>

        <!-- Mensaje motivacional -->
        <div class="mt-8 pt-6 border-t border-[#E0D5C7] text-center">
            <p class="text-sm text-[#825028] italic">
                "Todo Cojo, Todo Tiro" - Juntos construimos una economía circular
            </p>
        </div>
    </GuestLayout>
</template>
