<script setup>
import { onMounted, ref } from 'vue';
import { Moon, Palette, Sun } from '@lucide/vue';

const tema = ref(localStorage.getItem('temaVisual') || 'jovenes');
const modo = ref(localStorage.getItem('modoVisual') || '');

function aplicarTema() {
    document.body.classList.remove('tema-ninos', 'tema-jovenes', 'tema-adultos');
    document.body.classList.add(`tema-${tema.value}`);
    localStorage.setItem('temaVisual', tema.value);
}

function aplicarModo(nuevoModo = null) {
    if (nuevoModo) {
        modo.value = nuevoModo;
        localStorage.setItem('modoVisual', nuevoModo);
    }

    const hora = new Date().getHours();
    const modoCalculado = modo.value || (hora >= 18 || hora < 6 ? 'noche' : 'dia');
    document.body.classList.toggle('modo-noche', modoCalculado === 'noche');
}

onMounted(() => {
    aplicarTema();
    aplicarModo();
});
</script>

<template>
    <div class="controles-tema">
        <select v-model="tema" class="campo-select" aria-label="Seleccionar tema" @change="aplicarTema">
            <option value="ninos">Tema Ninos</option>
            <option value="jovenes">Tema Jovenes</option>
            <option value="adultos">Tema Adultos</option>
        </select>
        <button class="boton-icono" type="button" title="Modo dia" @click="aplicarModo('dia')">
            <Sun :size="18" />
        </button>
        <button class="boton-icono" type="button" title="Modo noche" @click="aplicarModo('noche')">
            <Moon :size="18" />
        </button>
        <button class="boton-icono" type="button" title="Aplicar tema">
            <Palette :size="18" />
        </button>
    </div>
</template>
