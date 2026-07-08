<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

defineProps({
    requisitos: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Requisitos" />
    <PublicLayout>
        <section class="bloque">
            <div class="contenedor">
                <h1 class="titulo-seccion">Requisitos de inscripcion</h1>
                <div class="tarjeta">
                    <ul>
                        <li v-for="requisito in requisitos" :key="requisito">{{ requisito }}</li>
                    </ul>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
