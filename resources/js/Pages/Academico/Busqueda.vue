<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({
    termino: { type: String, default: '' },
    resultados: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Busqueda interna" />
    <AppLayout>
        <section class="cabecera-pagina">
            <div>
                <h1>Busqueda interna</h1>
                <p class="texto-suave">Termino: {{ termino }}</p>
            </div>
        </section>

        <div class="grilla grilla-3">
            <article class="tarjeta">
                <h3>Estudiantes</h3>
                <p v-for="estudiante in resultados.estudiantes || []" :key="estudiante.id">
                    {{ estudiante.nombre }} {{ estudiante.apellido }} | {{ estudiante.cedula }}
                </p>
            </article>
            <article class="tarjeta">
                <h3>Cursos</h3>
                <p v-for="curso in resultados.cursos || []" :key="curso.id">
                    {{ curso.nombre }} | {{ curso.tipo_licencia }} | {{ curso.estado }}
                </p>
            </article>
            <article class="tarjeta">
                <h3>Inscripciones</h3>
                <p v-for="inscripcion in resultados.inscripciones || []" :key="inscripcion.id">
                    {{ inscripcion.estudiante }} | {{ inscripcion.curso }} | {{ inscripcion.estadoPago }}
                </p>
            </article>
        </div>
    </AppLayout>
</template>
