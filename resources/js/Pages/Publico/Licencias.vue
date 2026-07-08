<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

defineProps({
    licencias: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Licencias" />
    <PublicLayout>
        <section class="bloque">
            <div class="contenedor">
                <h1 class="titulo-seccion">Licencias disponibles</h1>
                <div class="grilla grilla-3">
                    <article v-for="licencia in licencias" :key="licencia.tipo" class="tarjeta">
                        <h3>{{ licencia.tipo }}</h3>
                        <p>{{ licencia.detalle }}</p>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
