<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

defineProps({
    termino: { type: String, default: '' },
    resultados: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Busqueda" />
    <PublicLayout>
        <section class="bloque">
            <div class="contenedor">
                <h1 class="titulo-seccion">Resultados de busqueda</h1>
                <p class="texto-suave">Termino: {{ termino }}</p>
                <div class="grilla grilla-3">
                    <article v-for="curso in resultados.cursos || []" :key="curso.id" class="tarjeta">
                        <h3>{{ curso.nombre }}</h3>
                        <p>{{ curso.descripcion }}</p>
                        <p>Licencia {{ curso.tipo_licencia }} | Bs {{ curso.precio }}</p>
                    </article>
                </div>
                <div class="tarjeta">
                    <h3>Requisitos encontrados</h3>
                    <ul>
                        <li v-for="requisito in resultados.requisitos || []" :key="requisito">{{ requisito }}</li>
                    </ul>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
