<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Award, FileText } from '@lucide/vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import PrimaryButton from '../../Components/PrimaryButton.vue';

defineProps({
    soloLectura: { type: Boolean, default: false },
    certificados: { type: Array, default: () => [] },
    aprobadosSinCertificado: { type: Array, default: () => [] },
});

function generarCertificado(inscripcion) {
    router.post(`/certificados/${inscripcion.id}/generar`);
}
</script>

<template>
    <Head title="Certificados" />
    <AppLayout>
        <section class="cabecera-pagina">
            <div>
                <h1>Certificados</h1>
                <p class="texto-suave">Emision de certificado simple para estudiantes aprobados.</p>
            </div>
        </section>

        <div class="grilla grilla-2">
            <article v-if="!soloLectura" class="tarjeta">
                <h3>Aprobados sin certificado</h3>
                <div v-for="inscripcion in aprobadosSinCertificado" :key="inscripcion.id" class="barra-contenido">
                    <span>{{ inscripcion.estudiante }} | {{ inscripcion.curso }} | Nota {{ inscripcion.nota }}</span>
                    <PrimaryButton type="button" @click="generarCertificado(inscripcion)">
                        <Award :size="18" />
                        Generar
                    </PrimaryButton>
                </div>
                <p v-if="!aprobadosSinCertificado.length" class="texto-suave">No hay certificados pendientes.</p>
            </article>

            <article class="tarjeta">
                <h3>Certificados emitidos</h3>
                <div v-for="certificado in certificados" :key="certificado.id" class="barra-contenido">
                    <span>{{ certificado.codigo }} | {{ certificado.estudiante }}</span>
                    <Link class="boton-secundario" :href="`/certificados/${certificado.id}`">
                        <FileText :size="18" />
                        Ver
                    </Link>
                </div>
                <p v-if="!certificados.length" class="texto-suave">Aun no existen certificados emitidos.</p>
            </article>
        </div>
    </AppLayout>
</template>
