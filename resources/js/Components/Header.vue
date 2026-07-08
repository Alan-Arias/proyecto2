<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { LogOut } from '@lucide/vue';
import AccessibilityControls from './AccessibilityControls.vue';
import SearchBar from './SearchBar.vue';
import ThemeSwitcher from './ThemeSwitcher.vue';

const pagina = usePage();

function cerrarSesion() {
    router.post('/logout');
}
</script>

<template>
    <header class="barra-app">
        <div class="contenedor barra-contenido">
            <Link class="marca" href="/dashboard">
                <img class="marca-logo" :src="'/images/logo-autoescuela-america.jpeg'" alt="Logo Autoescuela America" />
                <span>Autoescuela America</span>
            </Link>
            <SearchBar />
            <div class="acciones-header">
                <ThemeSwitcher />
                <AccessibilityControls />
                <span class="texto-suave">{{ pagina.props.auth?.usuario?.name }}</span>
                <button class="boton-icono" type="button" title="Cerrar sesion" @click="cerrarSesion">
                    <LogOut :size="18" />
                </button>
            </div>
        </div>
    </header>
</template>
