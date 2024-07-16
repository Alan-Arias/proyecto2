<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Promociones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49f6844fc6.js" crossorigin="anonymous"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
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
                        <a class="nav-link" href="/users"><i class="fas fa-users me-1"></i>Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/cliente"><i class="fas fa-address-card me-1"></i>Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/trabajadors"><i class="fas fa-hard-hat me-1"></i>Trabajador</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/vehiculos"><i class="fas fa-car me-1"></i>Reservas y Vehiculos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/servicios"><i class="fas fa-cogs me-1"></i>Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/promocion"><i class="fas fa-tag me-1"></i>Promociones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/detalle"><i class="fas fa-info-circle me-1"></i>Detalles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login"><i class="fas fa-info-circle me-1"></i>Login</a>
                    </li>
                </ul>
                
            </div>
        </div>
</nav>
    <h2>Registro de Promociones</h2>
    <br>
    @if(!empty($Promocion->id))
    @else
    <form action="/registrarPromocion" method="POST">
    @csrf
        <table>
            <tr>
                <td>Descripcion</td>
                <td><input type="text" id="descripcion" name="descripcion"></td>
            </tr>
            <tr>
                <td>Descuento (%)</td>
                <td><input type="text" id="descuento" name="descuento"></td>
            </tr>
            <tr>
                <td>Estado</td>
                <td>
                    <select name="estado" id="estado">
                        <option value="Disponible">Disponible</option>
                        <option value="No Disponible">No Disponible</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Fecha Inicio de la Promocion</td>
                <td><input type="date" name="fecha_inicio" id="fecha_inicio"></td>
            </tr>
            <tr>
                <td>Fecha Fin de la Promocion</td>
                <td><input type="date" name="fecha_fin" id="fecha_fin"></td>
            </tr>
            <tr>
                <td><input type="submit" class="btn btn-success"s value="Guardar"></td>
            </tr>
        </table>
    </form>
        @if (session('agregar'))
        <div class="alert alert-success mt-3">
                <p>{{ session('agregar') }}</p>
            </div>
        @endif
    @endif
    <br>
    <table class="table table-striped" border="1">
        <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th>Descuento (%)</th>
            <th>Estado</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
        </tr>
        @foreach ($PromocionServicio as $item)
        <tr>
            <td>{{ $item ->id }}</td>
            <td>{{ $item ->descripcion }}</td>
            <td>{{ $item ->descuento }}</td>
            <td>{{ $item ->estado }}</td>
            <td>{{ $item ->fecha_inicio }}</td>
            <td>{{ $item ->fecha_fin }}</td>
        </tr>
        @endforeach
    </table>
    <!--script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!--script -->
</body>
</html>