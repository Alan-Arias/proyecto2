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
        <textarea
            v-if="campo.tipo === 'textarea'"
            v-model="modelo"
            class="campo-textarea"
            :name="campo.nombre"
        />
        <span v-else-if="campo.tipo === 'checkbox'" class="controles-tema">
            <input v-model="modelo" :name="campo.nombre" type="checkbox" />
            <span>{{ modelo ? 'Si' : 'No' }}</span>
        </span>
        <input
            v-else
            v-model="modelo"
            class="campo"
            :name="campo.nombre"
            :type="campo.tipo || 'text'"
            :step="campo.tipo === 'number' ? '0.01' : undefined"
        />
        <small v-if="error" class="error-campo">{{ error }}</small>
    </label>
</template>
