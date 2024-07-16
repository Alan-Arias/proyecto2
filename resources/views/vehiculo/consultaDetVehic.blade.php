<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Detalle de Vehículo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49f6844fc6.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 56px;
        }
        .container {
            margin-top: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        .table-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="fas fa-cogs"></i> Taller Servimag</a>
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

<div class="container">
    <h1>Consulta Detalle de Vehículo</h1>
    <div class="table-container">
        @if (isset($detalles) && count($detalles) > 0)
            <h2>Resultados de la Consulta:</h2>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Año</th>
                        <th>Color</th>
                        <th>Placa</th>
                        <th>Asignación ID</th>
                        <th>Fecha Asignación</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detalles as $detalle)
                        <tr>                        
                            <td>{{ $detalle->marca }}</td>
                            <td>{{ $detalle->modelo }}</td>
                            <td>{{ $detalle->año }}</td>
                            <td>{{ $detalle->color }}</td>
                            <td>{{ $detalle->placa }}</td>
                            <td>{{ $detalle->asignacion_id }}</td>
                            <td>{{ $detalle->fecha_asignacion }}</td>
                            <td>{{ $detalle->estado }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif (isset($detalles) && count($detalles) == 0)
            <div class="alert alert-warning" role="alert">
                No se encontraron detalles para el vehículo especificado.
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
