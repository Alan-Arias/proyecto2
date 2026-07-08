<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

defineProps({
    cursosDestacados: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Inicio" />
    <PublicLayout>
        <section class="hero">
            <div class="hero-contenido">
                <h1>Autoescuela America</h1>
                <p>
                    Sistema academico para gestionar cursos de conduccion, inscripciones, pagos y seguimiento
                    academico en el municipio de Camiri.
                </p>
                <div class="acciones-header">
                    <Link class="boton" href="/cursos-publicos">Ver cursos</Link>
                    <Link class="boton-secundario" href="/requisitos">Requisitos</Link>
                </div>
            </div>
        </section>

        <section class="bloque">
            <div class="contenedor">
                <h2 class="titulo-seccion">Cursos disponibles</h2>
                <p class="texto-suave">Opciones de formacion vial pensadas para una gestion academica clara.</p>
                <div class="grilla grilla-3">
                    <article v-for="curso in cursosDestacados" :key="curso.id" class="tarjeta">
                        <h3>{{ curso.nombre }}</h3>
                        <p>{{ curso.descripcion }}</p>
                        <p><strong>Licencia:</strong> {{ curso.tipo_licencia }}</p>
                        <p><strong>Precio:</strong> Bs {{ curso.precio }}</p>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
