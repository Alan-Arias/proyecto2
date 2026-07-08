<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Inertia\Inertia;
use Inertia\Response;

class PaginaPublicaController extends Controller
{
    public function inicio(): Response
    {
        return Inertia::render('Publico/Inicio', [
            'cursosDestacados' => Curso::query()
                ->whereIn('estado', ['disponible', 'activo'])
                ->take(3)
                ->get(['id', 'nombre', 'descripcion', 'precio', 'tipo_licencia', 'tema_visual']),
        ]);
    }

    public function cursos(): Response
    {
        return Inertia::render('Publico/Cursos', [
            'cursos' => Curso::query()
                ->whereIn('estado', ['disponible', 'activo'])
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'descripcion', 'precio', 'duracion_horas', 'tipo_licencia', 'tema_visual']),
        ]);
    }

    public function licencias(): Response
    {
        return Inertia::render('Publico/Licencias', [
            'licencias' => [
                ['tipo' => 'Categoria B', 'detalle' => 'Vehiculos particulares para uso personal.'],
                ['tipo' => 'Categoria C', 'detalle' => 'Vehiculos de servicio publico segun normativa local.'],
                ['tipo' => 'Refuerzo practico', 'detalle' => 'Practica adicional para conductores con licencia.'],
            ],
        ]);
    }

    public function requisitos(): Response
    {
        return Inertia::render('Publico/Requisitos', [
            'requisitos' => [
                'Fotocopia de cedula de identidad vigente.',
                'Ser mayor de edad o cumplir la edad requerida por categoria.',
                'Cancelar la inscripcion o definir plan de cuotas.',
                'Asistir a clases teoricas y practicas programadas.',
            ],
        ]);
    }

    public function contacto(): Response
    {
        return Inertia::render('Publico/Contacto', [
            'contacto' => [
                'telefono' => '74812228',
                'whatsapp' => '74812228',
                'facebook' => 'Autoescuela America Camiri',
                'direccion' => 'Calle Comercio entre C/ Tcnel Sanchez y C/ German Bush',
            ],
        ]);
    }

    public function ubicacion(): Response
    {
        return Inertia::render('Publico/Ubicacion', [
            'referencia' => 'Calle Comercio entre C/ Tcnel Sanchez y C/ German Bush, Camiri, Santa Cruz, Bolivia.',
            'mapaUrl' => 'https://maps.app.goo.gl/xp8vi8qpaGGZdSKY8',
        ]);
    }
}
