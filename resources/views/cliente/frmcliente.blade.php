<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Clientes</title>
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
                    <a class="nav-link" href="/cliente"><i class="fas fa-money-bill-alt me-1"></i>Gestionar Clientes</a>
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
    <h1>Lista de Clientes</h1>
    @include('cliente.formulariocli', ['Cliente' => $Cliente])
    <table class="table table-striped" border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Sexo</th>
            <th>Id User</th>
            <th>Accion</th>
        </tr>
        @foreach ($Cliente as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->nombres }}</td>
            <td>{{ $item->edad }}</td>
            <td>{{ $item->sexo }}</td>
            <td>{{ $item->usuario_id }}</td>
            <td>
                <a href="/editar/{{ $item->id }}">Editar</a>
            </td>
        </tr>
        @endforeach
    </table>
    @if (session('mensaje'))
        <div class="alert alert-success mt-3">
            <p>{{ session('mensaje') }}</p>
        </div>
    @endif
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
