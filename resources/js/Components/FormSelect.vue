<script setup>
const modelo = defineModel();

defineProps({
    campo: { type: Object, required: true },
    error: { type: String, default: '' },
});
</script>

<template>
    <label class="grupo-campo">
        <span>{{ campo.etiqueta }}</span>
        <select
            v-model="modelo"
            class="campo-select"
            :name="campo.nombre"
            :multiple="campo.tipo === 'multiselect'"
        >
            <option v-if="campo.tipo !== 'multiselect'" value="">Seleccione una opcion</option>
            <option v-for="opcion in campo.opciones || []" :key="opcion.valor" :value="opcion.valor">
                {{ opcion.etiqueta }}
            </option>
        </select>
        <small v-if="error" class="error-campo">{{ error }}</small>
    </label>
</template>
