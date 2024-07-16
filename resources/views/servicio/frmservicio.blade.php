<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49f6844fc6.js" crossorigin="anonymous"></script>
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

<div class="container mt-4">
    <h2>Registro de Servicios</h2>
    <br>
    @if(isset($Servicios->id))
    <form action="/ActualizarServicio/{{ $Servicios->id }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $Servicios->nombre }}">
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ $Servicios->descripcion }}">
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <input type="text" class="form-control" id="estado" name="estado" value="{{ $Servicios->estado }}">
        </div>
        <button type="submit" class="btn btn-success">Actualizar</button>
    </form>
    @else
    <form action="/registrarServicio" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nombres" class="form-label">Nombres</label>
            <input type="text" class="form-control" id="nombres" name="nombres">
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion">
        </div>
        <div class="mb-3">
            <label for="costo" class="form-label">Costo del Servicio</label>
            <input type="text" class="form-control" id="costo" name="costo">
        </div>
        <div class="mb-3">
            <label for="duracion_estimada" class="form-label">Duración del Servicio (Horas)</label>
            <input type="text" class="form-control" id="duracion_estimada" name="duracion_estimada">
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado del Servicio</label>
            <select id="estado" name="estado" class="form-select">
                <option value="disponible">Disponible</option>
                <option value="no_disponible">No Disponible</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="id_trabajador" class="form-label">Trabajador Asignado</label>
            <select id="id_trabajador" name="id_trabajador" class="form-select">
                @foreach ($Trabajador as $item)
                <option value="{{ $item->id }}">{{ $item->nombres }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
    </form>
    @if (session('agregar'))
    <div class="alert alert-success mt-3" role="alert">
        {{ session('agregar') }}
    </div>
    @endif
    @endif

    <br>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Costo</th>
                <th>Duracion Estimada (horas)</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($Servicio as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->nombre }}</td>
                <td>{{ $item->descripcion }}</td>
                <td>{{ $item->costo }}</td>
                <td>{{ $item->duracion_estimada }}</td>
                <td>{{ $item->estado }}</td>
                <td>
                    <a href="/servicio/editar/{{ $item->id }}" class="btn btn-primary btn-sm">Editar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
