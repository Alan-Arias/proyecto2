<script setup>
import { Link } from '@inertiajs/vue3';
import AccessibilityControls from '../Components/AccessibilityControls.vue';
import Footer from '../Components/Footer.vue';
import SearchBar from '../Components/SearchBar.vue';
import ThemeSwitcher from '../Components/ThemeSwitcher.vue';
</script>

<template>
    <div class="pagina-publica">
        <header class="barra-publica">
            <div class="contenedor barra-contenido">
                <Link class="marca" href="/">
                    <img class="marca-logo" :src="'/images/logo-autoescuela-america.jpeg'" alt="Logo Autoescuela America" />
                    <span>Autoescuela America</span>
                </Link>
                <nav class="nav-publica" aria-label="Menu publico">
                    <Link href="/">Inicio</Link>
                    <Link href="/cursos-publicos">Cursos</Link>
                    <Link href="/licencias">Licencias</Link>
                    <Link href="/requisitos">Requisitos</Link>
                    <Link href="/contacto">Contacto</Link>
                    <Link href="/ubicacion">Ubicacion</Link>
                </nav>
                <div class="acciones-header">
                    <SearchBar />
                    <ThemeSwitcher />
                    <AccessibilityControls />
                    <Link class="boton" href="/login">Login</Link>
                </div>
            </div>
        </header>
        <slot />
        <Footer />
    </div>
</template>
