<script setup>
import { usePage } from '@inertiajs/vue3';
import Footer from '../Components/Footer.vue';
import Header from '../Components/Header.vue';
import Sidebar from '../Components/Sidebar.vue';

const pagina = usePage();
</script>

<template>
    <div class="pagina-app app-shell">
        <Sidebar />
        <div class="contenido-app">
            <Header />
            <main class="main-app">
                <div v-if="pagina.props.flash?.exito" class="mensaje exito">{{ pagina.props.flash.exito }}</div>
                <div v-if="pagina.props.flash?.error" class="mensaje error">{{ pagina.props.flash.error }}</div>
                <slot />
            </main>
            <Footer />
        </div>
    </div>
</template>
