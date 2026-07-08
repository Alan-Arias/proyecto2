<script setup>
defineProps({
    titulo: { type: String, required: true },
    valor: { type: [String, Number], required: true },
});
</script>

<template>
    <article class="tarjeta">
        <p class="texto-suave">{{ titulo }}</p>
        <h3>{{ valor }}</h3>
    </article>
</template>
