<script setup>
defineProps({
    total: { type: Number, default: 0 },
});
</script>

<template>
    <span>Visitas registradas: <strong>{{ total }}</strong></span>
</template>
