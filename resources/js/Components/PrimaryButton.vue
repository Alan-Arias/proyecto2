<script setup>
defineProps({
    type: { type: String, default: 'button' },
    variante: { type: String, default: 'primario' },
});
</script>

<template>
    <button :type="type" :class="variante === 'peligro' ? 'boton-peligro' : variante === 'secundario' ? 'boton-secundario' : 'boton'">
        <slot />
    </button>
</template>
