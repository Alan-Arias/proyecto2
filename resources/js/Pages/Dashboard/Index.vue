<script setup>
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import StatCard from '../../Components/StatCard.vue';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({
    rolPrincipal: { type: String, required: true },
    mostrarResumenGeneral: { type: Boolean, default: false },
    panelRol: { type: Object, required: true },
    estadisticas: { type: Object, default: () => ({}) },
    visitasPorPagina: { type: Array, default: () => [] },
    ultimasInscripciones: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <section class="cabecera-pagina">
            <div>
                <h1>{{ panelRol.titulo }}</h1>
                <p class="texto-suave">{{ panelRol.descripcion }}</p>
            </div>
        </section>

        <section class="bloque panel-rol">
            <div class="grilla grilla-4">
                <article v-for="tarjeta in panelRol.tarjetas" :key="tarjeta.titulo" class="tarjeta">
                    <h3>{{ tarjeta.titulo }}</h3>
                    <p class="numero-panel">{{ tarjeta.valor }}</p>
                    <span class="texto-suave">{{ tarjeta.descripcion }}</span>
                </article>
            </div>

            <div class="acciones-header accesos-rapidos">
                <Link
                    v-for="accion in panelRol.acciones"
                    :key="accion.etiqueta"
                    class="boton-secundario"
                    :href="accion.url"
                >
                    {{ accion.etiqueta }}
                </Link>
            </div>

            <div class="grilla grilla-2">
                <article v-for="lista in panelRol.listas" :key="lista.titulo" class="tarjeta">
                    <h3>{{ lista.titulo }}</h3>
                    <div v-if="!lista.items?.length" class="texto-suave">Sin datos para mostrar.</div>
                    <div v-for="item in lista.items" :key="`${item.nombre}-${item.detalle}`" class="barra-contenido">
                        <span>{{ item.nombre }}</span>
                        <strong>{{ item.detalle }}</strong>
                    </div>
                </article>
            </div>
        </section>

        <template v-if="mostrarResumenGeneral">
            <h2 class="titulo-seccion">Resumen general</h2>
            <div class="grilla grilla-4">
                <StatCard titulo="Estudiantes" :valor="estadisticas.totalEstudiantes" />
                <StatCard titulo="Cursos" :valor="estadisticas.totalCursos" />
                <StatCard titulo="Inscripciones" :valor="estadisticas.totalInscripciones" />
                <StatCard titulo="Ingresos" :valor="`Bs ${estadisticas.ingresos}`" />
            </div>

            <section class="bloque">
                <div class="grilla grilla-3">
                    <article class="tarjeta">
                        <h3>Aprobados</h3>
                        <p>{{ estadisticas.estudiantesAprobados }}</p>
                    </article>
                    <article class="tarjeta">
                        <h3>Reprobados</h3>
                        <p>{{ estadisticas.estudiantesReprobados }}</p>
                    </article>
                    <article class="tarjeta">
                        <h3>Cursos activos</h3>
                        <p>{{ estadisticas.cursosActivos }}</p>
                    </article>
                </div>
            </section>

            <section class="grilla grilla-2">
                <article class="tarjeta">
                    <h3>Ultimas inscripciones</h3>
                    <div class="tabla-contenedor">
                        <table>
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Curso</th>
                                    <th>Pago</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="inscripcion in ultimasInscripciones" :key="inscripcion.id">
                                    <td>{{ inscripcion.estudiante }}</td>
                                    <td>{{ inscripcion.curso }}</td>
                                    <td>{{ inscripcion.estadoPago }}</td>
                                    <td>Bs {{ inscripcion.saldoPendiente }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>
                <article class="tarjeta">
                    <h3>Visitas por pagina</h3>
                    <div v-for="visita in visitasPorPagina" :key="visita.pagina" class="barra-contenido">
                        <span>{{ visita.pagina }}</span>
                        <strong>{{ visita.visitas }}</strong>
                    </div>
                </article>
            </section>
        </template>
    </AppLayout>
</template>
