<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import PagoQrDetalle from '../../Components/PagoQr.vue';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({
    pagoQr: { type: Object, required: true },
});

const pagina = usePage();
const puedeGestionar = computed(() => {
    const roles = pagina.props.auth?.usuario?.roles || [];
    return roles.some((rol) => ['administrador', 'secretaria', 'propietario'].includes(rol));
});
</script>

<template>
    <Head title="Pago QR" />
    <AppLayout>
        <section class="cabecera-pagina">
            <div>
                <h1>Pago QR</h1>
                <p class="texto-suave">Generacion, consulta y confirmacion de pagos QR con PagoFacil.</p>
            </div>
        </section>

        <PagoQrDetalle :pago-qr="pagoQr" :puede-gestionar="puedeGestionar" />
    </AppLayout>
</template>
