<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { QrCode } from '@lucide/vue';
import PaginationLinks from '../../Components/PaginationLinks.vue';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({
    pagosQr: { type: Object, required: true },
});
</script>

<template>
    <Head title="Pagos QR" />
    <AppLayout>
        <section class="cabecera-pagina">
            <div>
                <h1>Pagos QR</h1>
                <p class="texto-suave">Transacciones academicas generadas para PagoFacil.</p>
            </div>
        </section>

        <section class="bloque">
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Curso</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Codigo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!pagosQr.data?.length">
                            <td colspan="6">No existen pagos QR registrados.</td>
                        </tr>
                        <tr v-for="pago in pagosQr.data" :key="pago.id">
                            <td>{{ pago.estudiante }}</td>
                            <td>{{ pago.curso }}</td>
                            <td>Bs {{ pago.monto }}</td>
                            <td>{{ pago.estado }}</td>
                            <td>{{ pago.codigo }}</td>
                            <td>
                                <Link class="boton-secundario" :href="`/pagos/qr/${pago.id}/mostrar`">
                                    <QrCode :size="16" />
                                    Ver QR
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <PaginationLinks :links="pagosQr.links || []" />
        </section>
    </AppLayout>
</template>
