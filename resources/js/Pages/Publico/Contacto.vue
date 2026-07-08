<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

defineProps({
    contacto: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Contacto" />
    <PublicLayout>
        <section class="bloque">
            <div class="contenedor">
                <h1 class="titulo-seccion">Contacto</h1>
                <div class="grilla grilla-3">
                    <article class="tarjeta">
                        <h3>Telefono</h3>
                        <p>{{ contacto.telefono }}</p>
                    </article>
                    <article class="tarjeta">
                        <h3>WhatsApp</h3>
                        <p>{{ contacto.whatsapp }}</p>
                    </article>
                    <article class="tarjeta">
                        <h3>Direccion</h3>
                        <p>{{ contacto.direccion }}</p>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
