<script setup>
import { usePage } from '@inertiajs/vue3';
import VisitCounter from './VisitCounter.vue';

const pagina = usePage();
</script>

<template>
    <footer class="footer">
        <div class="contenedor barra-contenido">
            <span>Autoescuela America, Camiri</span>
            <VisitCounter :total="Number(pagina.props.visitasFooter || 0)" />
        </div>
    </footer>
</template>
