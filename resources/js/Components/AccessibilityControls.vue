<script setup>
import { onMounted, ref } from 'vue';
import { Contrast, Type } from '@lucide/vue';

const escala = ref(Number(localStorage.getItem('escalaLetra') || 1));
const contraste = ref(localStorage.getItem('altoContraste') === 'true');

function aplicarLetra() {
    document.documentElement.style.setProperty('--escala-letra', escala.value.toString());
    localStorage.setItem('escalaLetra', escala.value.toString());
}

function aumentarLetra() {
    escala.value = Math.min(1.25, Number((escala.value + 0.05).toFixed(2)));
    aplicarLetra();
}

function disminuirLetra() {
    escala.value = Math.max(0.9, Number((escala.value - 0.05).toFixed(2)));
    aplicarLetra();
}

function alternarContraste() {
    contraste.value = !contraste.value;
    document.body.classList.toggle('alto-contraste', contraste.value);
    localStorage.setItem('altoContraste', contraste.value ? 'true' : 'false');
}

onMounted(() => {
    aplicarLetra();
    document.body.classList.toggle('alto-contraste', contraste.value);
});
</script>

<template>
    <div class="controles-accesibilidad">
        <button class="boton-icono" type="button" title="Aumentar letra" @click="aumentarLetra">
            <Type :size="19" />
            <span>+</span>
        </button>
        <button class="boton-icono" type="button" title="Disminuir letra" @click="disminuirLetra">
            <Type :size="16" />
            <span>-</span>
        </button>
        <button class="boton-icono" type="button" title="Alto contraste" @click="alternarContraste">
            <Contrast :size="18" />
        </button>
    </div>
</template>
