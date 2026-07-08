<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: { type: Array, default: () => [] },
});

function etiquetaPaginacion(label) {
    if (label === 'pagination.previous' || label.includes('Previous')) {
        return 'Atras';
    }

    if (label === 'pagination.next' || label.includes('Next')) {
        return 'Siguiente';
    }

    return label.replace('&laquo;', '').replace('&raquo;', '').trim();
}
</script>

<template>
    <div v-if="links?.length" class="acciones-header paginacion">
        <Link
            v-for="enlace in links"
            :key="enlace.label"
            class="boton-secundario"
            :class="{ deshabilitado: !enlace.url }"
            :href="enlace.url || '#'"
            :aria-disabled="!enlace.url"
            :tabindex="enlace.url ? 0 : -1"
        >
            {{ etiquetaPaginacion(enlace.label) }}
        </Link>
    </div>
</template>
