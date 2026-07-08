<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import DynamicMenu from './DynamicMenu.vue';

const pagina = usePage();
</script>

<template>
    <aside class="sidebar">
        <Link class="marca" href="/dashboard">
            <img class="marca-logo" :src="'/images/logo-autoescuela-america.jpeg'" alt="Logo Autoescuela America" />
            <span>America</span>
        </Link>
        <p class="texto-suave">Menu segun rol</p>
        <DynamicMenu :menus="pagina.props.menus || []" />
    </aside>
</template>
