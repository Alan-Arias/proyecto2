<script setup>
import { Head } from '@inertiajs/vue3';
import { Printer } from '@lucide/vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import PrimaryButton from '../../Components/PrimaryButton.vue';

defineProps({
    tipo: { type: String, required: true },
    datos: { type: Object, default: () => ({}) },
});

function imprimirDocumento() {
    window.print();
}
</script>

<template>
    <Head :title="tipo" />
    <AppLayout>
        <section class="documento">
            <h1>{{ tipo }}</h1>
            <p v-for="(valor, clave) in datos" :key="clave">
                <strong>{{ clave }}:</strong> {{ valor }}
            </p>
            <p class="texto-suave">
                Autoescuela America, municipio de Camiri.
            </p>
            <PrimaryButton type="button" @click="imprimirDocumento">
                <Printer :size="18" />
                Imprimir
            </PrimaryButton>
        </section>
    </AppLayout>
</template>
