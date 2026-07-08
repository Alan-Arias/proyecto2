<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({
    estudiantesInscritos: { type: Array, default: () => [] },
    pagosRecibidos: { type: Array, default: () => [] },
    cursosActivos: { type: Array, default: () => [] },
    aprobados: { type: Array, default: () => [] },
    reprobados: { type: Array, default: () => [] },
    visitas: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Reportes" />
    <AppLayout>
        <section class="cabecera-pagina">
            <div>
                <h1>Reportes</h1>
                <p class="texto-suave">Reportes simples para la defensa universitaria.</p>
            </div>
        </section>

        <div class="grilla grilla-2">
            <article class="tarjeta">
                <h3>Estudiantes inscritos</h3>
                <p v-for="item in estudiantesInscritos" :key="`${item.estudiante}-${item.curso}`">
                    {{ item.estudiante }} | {{ item.curso }} | {{ item.estadoPago }} | Bs {{ item.saldo }}
                </p>
            </article>
            <article class="tarjeta">
                <h3>Pagos recibidos</h3>
                <p v-for="item in pagosRecibidos" :key="`${item.estudiante}-${item.fecha}`">
                    {{ item.fecha }} | {{ item.estudiante }} | {{ item.metodo }} | Bs {{ item.monto }}
                </p>
            </article>
            <article class="tarjeta">
                <h3>Cursos activos</h3>
                <p v-for="curso in cursosActivos" :key="curso.nombre">
                    {{ curso.nombre }} | {{ curso.tipo_licencia }} | Bs {{ curso.precio }} | {{ curso.estado }}
                </p>
            </article>
            <article class="tarjeta">
                <h3>Aprobados y reprobados</h3>
                <h4>Aprobados</h4>
                <p v-for="item in aprobados" :key="`a-${item.estudiante}`">{{ item.estudiante }} | {{ item.nota }}</p>
                <h4>Reprobados</h4>
                <p v-for="item in reprobados" :key="`r-${item.estudiante}`">{{ item.estudiante }} | {{ item.nota }}</p>
            </article>
            <article class="tarjeta">
                <h3>Visitas por pagina</h3>
                <p v-for="visita in visitas" :key="visita.pagina">{{ visita.pagina }}: {{ visita.visitas }}</p>
            </article>
        </div>
    </AppLayout>
</template>
