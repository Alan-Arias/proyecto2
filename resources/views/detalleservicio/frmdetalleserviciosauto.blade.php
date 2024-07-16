<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Detalle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            padding-top: 4.5rem; /* Adjusted for fixed navbar height */
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030; /* Adjusted to overlay content */
        }
        h2 {
            margin-top: 3rem;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            text-align: center;
        }
        form {
            margin: 0 auto;
            max-width: 600px;
            padding: 1rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
        }
        table {
            width: 100%;
            margin-bottom: 1rem;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #dee2e6;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
        }
        .btn {
            margin-top: 1rem;
        }
        .alert {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 8px;
        }
        .table {
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="fas fa-cogs me-2"></i> Taller Servimag</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/users"><i class="fas fa-car me-1"></i>Gestionar Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/trabajadors"><i class="fas fa-money-bill-alt me-1"></i>Gestionar Trabajadores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/servicios"><i class="fas fa-money-bill-alt me-1"></i>Gestionar Servicios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/GestionarVehiculos"><i class="fas fa-money-bill-alt me-1"></i>Gestionar Vehiculos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/detalle"><i class="fas fa-money-bill-alt me-1"></i>Gestionar Detalles Vehiculo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/reserva"><i class="fas fa-money-bill-alt me-1"></i>Gestionar Reservas</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<h2>Registro de Detalle</h2>
<br>
<form id="detalleForm" action="/registrarDetalle" method="POST">
    @csrf
    <table class="table">
        <tr>
            <td>Seleccionar Vehiculo</td>
            <td>
                <select id="vehiculo_id" name="vehiculo_id" class="form-select">
                    @foreach ($Vehiculo as $ve)
                    <option value="{{ $ve->id }}" data-info="{{ $ve->marca }} - {{ $ve->modelo }} - {{ $ve->color }}">{{ $ve->marca }} {{ $ve->modelo }} {{ $ve->color }} - {{ $ve->cliente->nombres}}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td>Selecciona el Servicio Asignado</td>
            <td>
                <select id="asignacion_servicio_id" name="asignacion_servicio_id" class="form-select">
                    @foreach ($AsignacionServicio as $item)
                    <option value="{{ $item->id }}" data-info="{{ $item->servicio->nombre }}">{{ $item->fecha_asignacion }} - {{ $item->servicio->nombre }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td>Fecha Servicio</td>
            <td><input type="date" id="fecha_servicio" name="fecha_servicio" class="form-control"></td>
        </tr>
        <tr>
            <td><button type="button" onclick="agregarRegistro()" class="btn btn-primary">Añadir</button></td>
        </tr>
    </table>
    <br>
    <table class="table table-striped" border="1" id="tablaTemporal">
        <thead class="table-dark">
            <tr>
                <th>Vehiculo</th>
                <th>Servicio</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Filas añadidas aquí por JavaScript -->
        </tbody>
    </table>
    <br>
    <button class="btn btn-success" type="submit">Guardar</button>
</form>
@if (session('agregar'))
<div class="alert alert-success mt-3" role="alert">
    {{ session('agregar') }}
</div>
@endif

<button class="btn btn-success mt-3"><a href="/asigserv" class="text-white text-decoration-none">Asignación de Servicios</a></button>

<br><br>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Nombre</th>
            <th>Monto</th>
            <th>Vehículo</th>
            <th>Servicio</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($Detalle as $detalle)
        <tr>
            <td>{{ $detalle->vehiculo->cliente->nombres}}</td>
            <td>{{ optional(optional($detalle->asignacion_servicio)->servicio)->costo ?? 'Nada que Mostrar'}}</td>
            <td>{{ $detalle->vehiculo->marca }} - {{ $detalle->vehiculo->modelo }} - {{ $detalle->vehiculo->color }}</td>
            <td>{{ optional(optional($detalle->asignacion_servicio)->servicio)->nombre ?? 'No asignado' }}</td>
            <td>{{ $detalle->fecha_servicio }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    function agregarRegistro() {
        const vehiculoSelect = document.getElementById('vehiculo_id');
        const servicioSelect = document.getElementById('asignacion_servicio_id');
        const fecha = document.getElementById('fecha_servicio').value;

        if (!fecha) {
            alert("Debe seleccionar una fecha");
            return;
        }

        const vehiculoInfo = vehiculoSelect.options[vehiculoSelect.selectedIndex].dataset.info;
        const servicioInfo = servicioSelect.options[servicioSelect.selectedIndex].dataset.info;
        const vehiculoId = vehiculoSelect.value;
        const servicioId = servicioSelect.value;

        const tbody = document.getElementById('tablaTemporal').getElementsByTagName('tbody')[0];
        const newRow = tbody.insertRow();

        const celdaVehiculo = newRow.insertCell(0);
        const celdaServicio = newRow.insertCell(1);
        const celdaFecha = newRow.insertCell(2);
        const celdaAcciones = newRow.insertCell(3);

        celdaVehiculo.innerHTML = `<input type="hidden" name="vehiculo_ids[]" value="${vehiculoId}">${vehiculoInfo}`;
        celdaServicio.innerHTML = `<input type="hidden" name="asignacion_servicio_ids[]" value="${servicioId}">${servicioInfo}`;
        celdaFecha.innerHTML = `<input type="hidden" name="fechas_servicio[]" value="${fecha}">${fecha}`;
        celdaAcciones.innerHTML = '<button type="button" onclick="eliminarRegistro(this)" class="btn btn-danger">Eliminar</button>';
    }

    function eliminarRegistro(boton) {
        const fila = boton.parentNode.parentNode;
        fila.parentNode.removeChild(fila);
    }
</script>

</body>
</html
