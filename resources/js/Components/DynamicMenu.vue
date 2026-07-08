<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    ClipboardCheck,
    CreditCard,
    FileText,
    GraduationCap,
    LayoutDashboard,
    Menu as MenuIcon,
    QrCode,
    Settings,
    Users,
} from '@lucide/vue';

defineProps({
    menus: { type: Array, default: () => [] },
});

const pagina = usePage();

const iconos = {
    panel: LayoutDashboard,
    usuarios: Users,
    roles: Users,
    estudiantes: GraduationCap,
    instructores: Users,
    materias: BookOpen,
    cursos: BookOpen,
    ofertas: ClipboardCheck,
    inscripciones: ClipboardCheck,
    asistencia: ClipboardCheck,
    notas: FileText,
    certificados: FileText,
    pagos: CreditCard,
    'pago-qr': QrCode,
    reportes: FileText,
    estadisticas: LayoutDashboard,
    menu: MenuIcon,
    configuracion: Settings,
};

const rutas = {
    dashboard: '/dashboard',
    'usuarios.index': '/usuarios',
    'roles.index': '/roles',
    'estudiantes.index': '/estudiantes',
    'instructores.index': '/instructores',
    'materias.index': '/materias',
    'cursos.index': '/cursos',
    'ofertas.index': '/ofertas',
    'inscripciones.index': '/inscripciones',
    'asistencia.index': '/asistencia',
    'calificaciones.index': '/calificaciones',
    'certificados.index': '/certificados',
    'pagos.index': '/pagos',
    'pagos.qr.index': '/pagos/qr',
    'reportes.index': '/reportes',
    'estadisticas.index': '/estadisticas',
    'menus.index': '/menus',
    'configuracion.index': '/configuracion',
};

const urlActual = computed(() => pagina.url);

function resolverUrl(ruta) {
    return rutas[ruta] || '/dashboard';
}

function resolverIcono(nombre) {
    return iconos[nombre] || MenuIcon;
}
</script>

<template>
    <nav class="sidebar-menu" aria-label="Menu principal">
        <Link
            v-for="menu in menus"
            :key="menu.id"
            class="enlace-menu"
            :class="{ activo: urlActual.startsWith(resolverUrl(menu.ruta)) }"
            :href="resolverUrl(menu.ruta)"
        >
            <component :is="resolverIcono(menu.icono)" :size="18" />
            <span>{{ menu.nombre }}</span>
        </Link>
    </nav>
</template>
