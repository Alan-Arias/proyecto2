<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { LogIn } from '@lucide/vue';
import PrimaryButton from '../../Components/PrimaryButton.vue';
import PublicLayout from '../../Layouts/PublicLayout.vue';

const formulario = useForm({
    email: '',
    password: '',
    remember: false,
});

function iniciarSesion() {
    formulario.post('/login');
}
</script>

<template>
    <Head title="Login" />
    <PublicLayout>
        <section class="bloque">
            <div class="contenedor">
                <div class="tarjeta" style="max-width: 480px; margin: 0 auto;">
                    <h1 class="titulo-seccion">Iniciar sesion</h1>
                    <form class="formulario" @submit.prevent="iniciarSesion">
                        <label class="grupo-campo">
                            <span>Correo electronico</span>
                            <input v-model="formulario.email" class="campo" type="email" autocomplete="username" />
                            <small v-if="formulario.errors.email" class="error-campo">{{ formulario.errors.email }}</small>
                        </label>
                        <label class="grupo-campo">
                            <span>Contrasena</span>
                            <input v-model="formulario.password" class="campo" type="password" autocomplete="current-password" />
                            <small v-if="formulario.errors.password" class="error-campo">{{ formulario.errors.password }}</small>
                        </label>
                        <label class="controles-tema">
                            <input v-model="formulario.remember" type="checkbox" />
                            <span>Recordar sesion</span>
                        </label>
                        <PrimaryButton type="submit">
                            <LogIn :size="18" />
                            Entrar
                        </PrimaryButton>
                    </form>
                    <p class="texto-suave">
                       Ingrese sus credenciales
                    </p>
                    <Link href="/">Volver al inicio</Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
