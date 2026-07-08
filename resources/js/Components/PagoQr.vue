<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Ban, CheckCircle2, RefreshCw } from '@lucide/vue';
import PrimaryButton from './PrimaryButton.vue';

const props = defineProps({
    pagoQr: { type: Object, required: true },
    puedeGestionar: { type: Boolean, default: false },
});

const qrImagen = computed(() => props.pagoQr.codigoQr || props.pagoQr.qrContentUrl || props.pagoQr.urlQr);
const estaConfirmado = computed(() => props.pagoQr.estadoTransaccion === 'confirmado');
const estaCerrado = computed(() => ['confirmado', 'cancelado', 'vencido'].includes(props.pagoQr.estadoTransaccion));
const enlacesPagoFacil = computed(() => [
    { etiqueta: 'Abrir checkout', url: props.pagoQr.checkoutUrl },
    { etiqueta: 'Abrir enlace universal', url: props.pagoQr.universalUrl },
    { etiqueta: 'Abrir app bancaria', url: props.pagoQr.deepLink },
].filter((enlace) => enlace.url));

function consultarEstado() {
    router.post(`/pagos/qr/${props.pagoQr.id}/consultar`, {}, { preserveScroll: true });
}

function confirmarPago() {
    router.post(`/pagos/qr/${props.pagoQr.id}/confirmar`, {}, { preserveScroll: true });
}

function cancelarPago() {
    if (confirm('Desea cancelar este pago QR?')) {
        router.post(`/pagos/qr/${props.pagoQr.id}/cancelar`, {}, { preserveScroll: true });
    }
}
</script>

<template>
    <article class="pago-qr-panel">
        <div class="pago-qr-datos">
            <span class="etiqueta-estado" :class="`estado-${pagoQr.estadoTransaccion}`">
                {{ pagoQr.estadoTransaccion }}
            </span>
            <h2>Pago QR PagoFacil</h2>
            <p class="texto-suave">QR generado con PagoFacil para completar el pago del curso.</p>

            <dl class="detalle-lista">
                <div>
                    <dt>Estudiante</dt>
                    <dd>{{ pagoQr.estudiante }}</dd>
                </div>
                <div>
                    <dt>Curso</dt>
                    <dd>{{ pagoQr.curso }}</dd>
                </div>
                <div>
                    <dt>Monto a pagar</dt>
                    <dd>Bs {{ pagoQr.monto }}</dd>
                </div>
                <div>
                    <dt>Saldo pendiente</dt>
                    <dd>Bs {{ pagoQr.saldoPendiente }}</dd>
                </div>
                <div>
                    <dt>Codigo de transaccion</dt>
                    <dd>{{ pagoQr.codigoTransaccion }}</dd>
                </div>
                <div>
                    <dt>Vencimiento</dt>
                    <dd>{{ pagoQr.fechaVencimiento }}</dd>
                </div>
            </dl>

            <p v-if="estaConfirmado" class="mensaje exito">El pago QR fue confirmado correctamente.</p>

            <div v-if="enlacesPagoFacil.length" class="acciones-header">
                <a
                    v-for="enlace in enlacesPagoFacil"
                    :key="enlace.etiqueta"
                    class="boton-secundario"
                    :href="enlace.url"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    {{ enlace.etiqueta }}
                </a>
            </div>

            <div class="acciones-header">
                <PrimaryButton type="button" variante="secundario" @click="consultarEstado">
                    <RefreshCw :size="18" />
                    Consultar estado
                </PrimaryButton>
                <PrimaryButton
                    v-if="puedeGestionar && !estaConfirmado"
                    type="button"
                    @click="confirmarPago"
                >
                    <CheckCircle2 :size="18" />
                    Confirmar pago
                </PrimaryButton>
                <PrimaryButton
                    v-if="puedeGestionar && !estaCerrado"
                    type="button"
                    variante="peligro"
                    @click="cancelarPago"
                >
                    <Ban :size="18" />
                    Cancelar
                </PrimaryButton>
            </div>
        </div>

        <div class="pago-qr-imagen">
            <img v-if="qrImagen" :src="qrImagen" alt="Codigo QR academico de PagoFacil" />
            <p class="texto-suave">Escanea el codigo con tu aplicacion bancaria y luego consulta el estado.</p>
        </div>
    </article>
</template>
