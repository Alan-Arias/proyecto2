<script setup>
import { Link } from '@inertiajs/vue3';
import { Edit, ExternalLink, Trash2 } from '@lucide/vue';

defineProps({
    columnas: { type: Object, required: true },
    registros: { type: Object, required: true },
    acciones: { type: Array, default: () => [] },
    soloLectura: { type: Boolean, default: false },
});

defineEmits(['editar', 'eliminar']);

function valor(registro, clave) {
    return registro[clave] ?? '';
}

function urlAccion(accion, registro) {
    return accion.url.replace('{id}', registro.id);
}
</script>

<template>
    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr>
                    <th v-for="(etiqueta, clave) in columnas" :key="clave">{{ etiqueta }}</th>
                    <th v-if="!soloLectura || acciones.length">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!registros.data?.length">
                    <td :colspan="Object.keys(columnas).length + (!soloLectura || acciones.length ? 1 : 0)">No existen registros para mostrar.</td>
                </tr>
                <tr v-for="registro in registros.data" :key="registro.id">
                    <td v-for="(etiqueta, clave) in columnas" :key="clave">{{ valor(registro, clave) }}</td>
                    <td v-if="!soloLectura || acciones.length">
                        <div class="acciones-tabla">
                            <button v-if="!soloLectura && !registro.ocultarEditar" class="boton-icono" type="button" title="Editar" @click="$emit('editar', registro)">
                                <Edit :size="17" />
                            </button>
                            <button v-if="!soloLectura && !registro.ocultarEliminar" class="boton-icono" type="button" title="Eliminar" @click="$emit('eliminar', registro)">
                                <Trash2 :size="17" />
                            </button>
                            <Link
                                v-for="accion in acciones"
                                :key="accion.etiqueta"
                                class="boton-secundario"
                                :href="urlAccion(accion, registro)"
                            >
                                <ExternalLink :size="16" />
                                {{ accion.etiqueta }}
                            </Link>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
