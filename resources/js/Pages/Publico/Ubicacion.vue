<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

defineProps({
    referencia: { type: String, default: '' },
    mapaUrl: { type: String, default: '' },
});
</script>

<template>
    <Head title="Ubicacion" />
    <PublicLayout>
        <section class="bloque">
            <div class="contenedor">
                <h1 class="titulo-seccion">Ubicacion</h1>
                <div class="tarjeta">
                    <p>{{ referencia }}</p>
                    <p class="texto-suave">Camiri, Santa Cruz, Bolivia.</p>
                    <a
                        v-if="mapaUrl"
                        class="boton primario"
                        :href="mapaUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Ver en Google Maps
                    </a>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
