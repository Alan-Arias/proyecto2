<script setup>
import { Head } from '@inertiajs/vue3';
import StatCard from '../../Components/StatCard.vue';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({
    tarjetas: { type: Array, default: () => [] },
    visitas: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Estadisticas" />
    <AppLayout>
        <section class="cabecera-pagina">
            <div>
                <h1>Estadisticas</h1>
                <p class="texto-suave">Indicadores basicos del sistema.</p>
            </div>
        </section>

        <div class="grilla grilla-4">
            <StatCard v-for="tarjeta in tarjetas" :key="tarjeta.titulo" :titulo="tarjeta.titulo" :valor="tarjeta.valor" />
        </div>

        <section class="bloque">
            <article class="tarjeta">
                <h3>Visitas por pagina</h3>
                <div v-for="visita in visitas" :key="visita.pagina" class="barra-contenido">
                    <span>{{ visita.pagina }}</span>
                    <strong>{{ visita.visitas }}</strong>
                </div>
            </article>
        </section>
    </AppLayout>
</template>
