<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Reserva Servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49f6844fc6.js" crossorigin="anonymous"></script>
  
</head>
<body>
@extends('app')

@section('title', 'Inicio')

@section('content')
    @if(!empty($Vehiculo->id))
    @else
    <div class="container">
    <div class="form-container">
        <form action="/registrarVehiculo" method="POST" class="forms">
            @csrf
            <div>
                <h2>Registro de Vehículo</h2>
                <div class="mb-3">
                    <label for="nombre_cliente" class="form-label">Cliente</label>
                    <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" readonly>                    
                </div>
                <div class="mb-3" hidden>
                    <input type="hidden" id="id" name="id">
                </div>
                <div class="mb-3">
                    <label for="marca" class="form-label">Marca del Vehiculo</label>
                    <input type="text" class="form-control" id="marca" name="marca">
                </div>
                <div class="mb-3">
                    <label for="modelo" class="form-label">Modelo del Vehiculo</label>
                    <input type="text" class="form-control" id="modelo" name="modelo">
                </div>
                <div class="mb-3">
                    <label for="año" class="form-label">Año del Vehiculo</label>
                    <input type="text" class="form-control" id="año" name="año">
                </div>
                <div class="mb-3">
                    <label for="color" class="form-label">Color del Vehiculo</label>
                    <input type="text" class="form-control" id="color" name="color">
                </div>
                <div class="mb-3">
                    <label for="placa" class="form-label">Placa del Vehiculo</label>
                    <input type="text" class="form-control" id="placa" name="placa">
                </div>
            </div>
            <div>
                <h2>Hacer una Reserva</h2>
                <div class="mb-3">
                    <label for="fecha_reserva" class="form-label">Fecha de la Reserva</label>
                    <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva">
                </div>
                <div class="mb-3">
                    <label for="detalle" class="form-label">Detalle el Problema</label>
                    <textarea class="form-control" id="detalle" name="detalle"></textarea>
                </div>
            </div>
            <div style="text-align: center;">
                <button type="submit" class="btn btn-primary">Guardar Datos</button>
            </div>
        </form>
    </div>
</div>


<script>
    function seleccionarCliente(id, nombre) {
        document.getElementById('id').value = id;
        document.getElementById('nombre_cliente').value = nombre;
        var modal = bootstrap.Modal.getInstance(document.getElementById('clienteModal'));
        modal.hide();
    }
</script>
        
    </div>
    <div hidden>
            <h2>Asignación de Servicios</h2>
                <table>
                    <form action="/registrarServicio" method="POST">
                    <tr>
                        <td>Fecha Actual</td>
                        <td><input type="date" id="fecha_asignacion" name="fecha_asignacion"></td>
                    </tr>
                    <tr>
                        <td>Estado</td>
                        <td>
                            <select name="estado" id="estado">
                                <option value="en progreso">En Progreso</option>
                                <option value="terminado">Terminado</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Trabajador</td>
                        <td>
                            <select id="trabajador" name="trabajador">
                                @foreach ($Trabajador as $item)
                                    <option value="{{ $item->id }}">{{ $item->nombres }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Servicio</td>
                        <td>
                            <select id="servicio" name="servicio">
                                @foreach ($Servicio as $item)
                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                <tr>                    
                    <td>
                        <input type="submit" class="btn btn-success" value="Guardar Datos">
                    </td>
                </tr>
                    </form>                    
                </table>
            </div>            
            @if (session('agregar'))
        <div class="alert alert-success mt-3">
                <p>{{ session('agregar') }}</p>
            </div>
        @endif
    @endif
    <br>    
    @php
    $clienteNombre = session('nombres');
    $totalCosto = 0;
@endphp

<h2>Tabla de Vehículos y Detalles</h2>
<table class="table table-striped table-bordered" border="1">
    <tr>
        <th>Nombre</th>
        <th>Marca</th>
        <th>Modelo</th>
        <th>Año</th>
        <th>Color</th>
        <th>Placa</th>
        <th>Costo del Servicio</th>
        <th>Servicio</th>
        <th>Fecha del Servicio</th>
    </tr>
    @foreach ($Vehiculo as $item)
        @if (strpos($item->cliente->nombres, $clienteNombre) !== false)
            @php
                $detalles = $Detalle->where('vehiculo_id', $item->id);
            @endphp
            @if ($detalles->isEmpty())
                <tr>
                    <td>{{ $item->cliente->nombres }}</td>
                    <td>{{ $item->marca }}</td>
                    <td>{{ $item->modelo }}</td>
                    <td>{{ $item->año }}</td>
                    <td>{{ $item->color }}</td>
                    <td>{{ $item->placa }}</td>
                    <td colspan="3">Sin detalles de servicio</td>
                </tr>
            @else
                @foreach ($detalles as $detalle)
                    @php
                        $costoServicio = optional(optional($detalle->asignacion_servicio)->servicio)->costo ?? 0;
                        $totalCosto += $costoServicio;
                    @endphp
                    <tr>
                        <td>{{ $item->cliente->nombres }}</td>
                        <td>{{ $item->marca }}</td>
                        <td>{{ $item->modelo }}</td>
                        <td>{{ $item->año }}</td>
                        <td>{{ $item->color }}</td>
                        <td>{{ $item->placa }}</td>
                        <td>{{ $costoServicio != 0 ? $costoServicio : 'Nada que Mostrar' }}</td>
                        <td>{{ optional(optional($detalle->asignacion_servicio)->servicio)->nombre ?? 'No asignado' }}</td>
                        <td>{{ $detalle->fecha_servicio ?? 'Sin fecha' }}</td>
                    </tr>
                @endforeach
            @endif
        @endif
    @endforeach
</table>

    <!--<h2>Tabla Reservas</h2>
    <table class="table table-striped" border="1">
    <tr>        
        <th>Fecha de la Reserva</th>
        <th>Cliente</th>
        <th>Detalle del Problema</th>
    </tr>
    @foreach ($Reserva as $item)
    <tr>
        <td>{{ $item->fecha_reserva }}</td>
        <td>{{ optional($item->cliente)->nombres }}</td>
        <td>{{ $item->detalle }}</td>
    </tr>
    @endforeach
    </table>-->
<!-- Modal -->
<div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clienteModalLabel">Seleccionar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Edad</th>
                            <th>Sexo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Cliente as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->nombres }}</td>
                            <td>{{ $item->edad }}</td>
                            <td>{{ $item->sexo }}</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="seleccionarCliente('{{ $item->id }}', '{{ $item->nombres }}')">
                                    Añadir
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

    
    <!--script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!--script -->
    <script>

        const hoy = new Date();


        const fechaFormateada = hoy.toISOString().split('T')[0];

      
        document.getElementById('fecha_asignacion').value = fechaFormateada;
    </script>
<script>
    
    var clienteId = '{{ session('cliente_id') }}'; 
    var clienteNombre = '{{ session('nombres') }}';

    document.getElementById('id').value = clienteId;
    document.getElementById('nombre_cliente').value = clienteNombre;
</script>
@endsection
</body>
</html>