<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Search } from '@lucide/vue';

const termino = ref('');

function buscar() {
    router.get('/buscar', { q: termino.value }, { preserveState: false });
}
</script>

<template>
    <form class="buscador" @submit.prevent="buscar">
        <input v-model="termino" type="search" placeholder="Buscar cursos, estudiantes o pagos" aria-label="Buscar" />
        <button class="boton-icono" type="submit" title="Buscar">
            <Search :size="18" />
        </button>
    </form>
</template>
