<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido Cliente - Taller Servimag</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49f6844fc6.js" crossorigin="anonymous"></script>
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
                    <a class="nav-link" href="/vehiculosview"><i class="fas fa-car me-1"></i>Ver Vehículos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/asignacionserview"><i class="fas fa-money-bill-alt me-1"></i>Asignacion de Servicios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/trabjview"><i class="fas fa-money-bill-alt me-1"></i>Trabajadores</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<table class="table table-striped" border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Sexo</th>
            <th>Id User</th>
        </tr>
        @foreach ($Trabajador as $item)
        <tr>
            <td>{{ $item ->id }}</td>
            <td>{{ $item ->nombres }}</td>
            <td>{{ $item ->edad }}</td>
            <td>{{ $item ->sexo }}</td>
            <td>{{ $item ->usuario_id }}</td>
        </tr>
        @endforeach
    </table>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>