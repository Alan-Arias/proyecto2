<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Plus, Save, X } from '@lucide/vue';
import DataTable from '../../Components/DataTable.vue';
import FormInput from '../../Components/FormInput.vue';
import FormSelect from '../../Components/FormSelect.vue';
import PaginationLinks from '../../Components/PaginationLinks.vue';
import PrimaryButton from '../../Components/PrimaryButton.vue';
import AppLayout from '../../Layouts/AppLayout.vue';

const props = defineProps({
    modulo: { type: Object, required: true },
    registros: { type: Object, required: true },
});

const modoEdicion = ref(false);
const registroActual = ref(null);
const pagina = usePage();
const rolesUsuario = computed(() => pagina.props.auth?.usuario?.roles || []);

function valoresIniciales() {
    return props.modulo.campos.reduce((valores, campo) => {
        if (campo.tipo === 'multiselect') {
            valores[campo.nombre] = [];
        } else if (campo.tipo === 'checkbox') {
            valores[campo.nombre] = true;
        } else {
            valores[campo.nombre] = '';
        }

        return valores;
    }, {});
}

const formulario = useForm(valoresIniciales());

function cumpleCondicion(campo) {
    if (!campo.mostrarSi) {
        return true;
    }

    return formulario[campo.mostrarSi.campo] === campo.mostrarSi.valor;
}

function cumpleRol(campo) {
    if (!campo.soloRoles?.length) {
        return true;
    }

    return campo.soloRoles.some((rol) => rolesUsuario.value.includes(rol));
}

const camposVisibles = computed(() => props.modulo.campos.filter((campo) => {
    const esVisibleEnModo = !modoEdicion.value || !campo.soloCrear;

    return campo.tipo !== 'hidden' && esVisibleEnModo && cumpleCondicion(campo) && cumpleRol(campo);
}));

function limpiarFormulario() {
    modoEdicion.value = false;
    registroActual.value = null;
    formulario.defaults(valoresIniciales());
    formulario.reset();
    formulario.clearErrors();
}

function editar(registro) {
    const crearDesdeRegistro = Boolean(registro.crearUsuarioDesdeInstructor || registro.crearUsuarioDesdePersona);
    modoEdicion.value = !crearDesdeRegistro;
    registroActual.value = crearDesdeRegistro ? null : registro;
    props.modulo.campos.forEach((campo) => {
        if (campo.tipo === 'multiselect') {
            formulario[campo.nombre] = Array.from(registro[campo.nombre] || []);
        } else if (campo.tipo === 'checkbox') {
            formulario[campo.nombre] = Boolean(registro[campo.nombre]);
        } else {
            formulario[campo.nombre] = registro[campo.nombre] ?? '';
        }
    });
    formulario.clearErrors();
}

function guardar() {
    if (props.modulo.soloLectura) {
        return;
    }

    if (formulario.forma_pago !== 'cuotas' && 'numero_cuotas' in formulario) {
        formulario.numero_cuotas = '';
    }

    if (modoEdicion.value && registroActual.value) {
        formulario.put(`/${props.modulo.ruta}/${registroActual.value.id}`, {
            onSuccess: () => limpiarFormulario(),
        });
        return;
    }

    formulario.post(`/${props.modulo.ruta}`, {
        onSuccess: () => limpiarFormulario(),
    });
}

function eliminar(registro) {
    if (confirm('Desea eliminar este registro?')) {
        router.delete(`/${props.modulo.ruta}/${registro.id}`);
    }
}
</script>

<template>
    <Head :title="modulo.titulo" />
    <AppLayout>
        <section class="cabecera-pagina">
            <div>
                <h1>{{ modulo.titulo }}</h1>
                <p class="texto-suave">{{ modulo.descripcion }}</p>
            </div>
            <PrimaryButton v-if="!modulo.soloLectura" variante="secundario" @click="limpiarFormulario">
                <Plus :size="18" />
                Nuevo
            </PrimaryButton>
        </section>

        <section v-if="!modulo.soloLectura" class="tarjeta">
            <form class="formulario" @submit.prevent="guardar">
                <div class="formulario-grid">
                    <template v-for="campo in camposVisibles" :key="campo.nombre">
                        <FormSelect
                            v-if="campo.tipo === 'select' || campo.tipo === 'multiselect'"
                            v-model="formulario[campo.nombre]"
                            :campo="campo"
                            :error="formulario.errors[campo.nombre]"
                        />
                        <FormInput
                            v-else
                            v-model="formulario[campo.nombre]"
                            :campo="campo"
                            :error="formulario.errors[campo.nombre]"
                        />
                    </template>
                </div>
                <div class="acciones-header">
                    <PrimaryButton type="submit">
                        <Save :size="18" />
                        {{ modoEdicion ? 'Actualizar' : 'Guardar' }}
                    </PrimaryButton>
                    <PrimaryButton type="button" variante="secundario" @click="limpiarFormulario">
                        <X :size="18" />
                        Cancelar
                    </PrimaryButton>
                </div>
            </form>
        </section>

        <section class="bloque">
            <DataTable
                :columnas="modulo.columnas"
                :registros="registros"
                :acciones="modulo.acciones || []"
                :solo-lectura="modulo.soloLectura || false"
                @editar="editar"
                @eliminar="eliminar"
            />
            <PaginationLinks :links="registros.links || []" />
        </section>
    </AppLayout>
</template>
