<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

defineProps({
    cursos: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Cursos" />
    <PublicLayout>
        <section class="bloque">
            <div class="contenedor">
                <h1 class="titulo-seccion">Cursos</h1>
                <div class="grilla grilla-3">
                    <article v-for="curso in cursos" :key="curso.id" class="tarjeta">
                        <h3>{{ curso.nombre }}</h3>
                        <p>{{ curso.descripcion }}</p>
                        <p><strong>Duracion:</strong> {{ curso.duracion_horas }} horas</p>
                        <p><strong>Licencia:</strong> {{ curso.tipo_licencia }}</p>
                        <p><strong>Precio:</strong> Bs {{ curso.precio }}</p>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
