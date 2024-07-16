<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Reserva Servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49f6844fc6.js" crossorigin="anonymous"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        /* Estilo adicional para separar los formularios y la tabla */
        .forms-container {
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .table-container {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .forms {
            display: flex;
            gap: 20px; /* Espacio entre los formularios */
        }

        form {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
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

<div class="container forms-container">
    @if(!empty($Vehiculo->id))
    @else
    <div class="forms">
        <form action="/GuardarVehiculo" method="POST">
            @csrf
            <h2>Registro de Vehículo</h2>
            <table>
                <tr>
                    <td>Cliente</td>
                    <td>
                        <input type="hidden" id="id" name="id">
                        <input type="text" id="nombre_cliente" name="nombre_cliente" readonly>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#clienteModal">
                            Seleccionar Cliente
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>Marca del Vehiculo</td>
                    <td><input type="text" id="marca" name="marca"></td>
                </tr>
                <tr>
                    <td>Modelo del Vehiculo</td>
                    <td><input type="text" id="modelo" name="modelo"></td>
                </tr>
                <tr>
                    <td>Año del Vehiculo</td>
                    <td><input type="text" id="año" name="año"></td>
                </tr>
                <tr>
                    <td>Color del Vehiculo</td>
                    <td><input type="text" id="color" name="color"></td>
                </tr>
                <tr>
                    <td>Placa del Vehiculo</td>
                    <td><input type="text" id="placa" name="placa"></td>
                </tr>
                <tr>
                    <td><input type="submit" class="btn btn-success" value="Guardar Datos"></td>
                </tr>
            </table>
            @if (session('agregar'))
                <div class="alert alert-success mt-3">
                    <p>{{ session('agregar') }}</p>
                </div>
            @endif
        </form>
    </div>
    @endif
</div>

<div class="container table-container">
    <h2>Tabla Vehiculos</h2>
    <table class="table table-striped" border="1">
        <tr>
            <th>Nombre</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Año</th>
            <th>Color</th>
            <th>Placa</th>
            <th>Acción</th> <!-- Nuevo encabezado para el botón -->
        </tr>
        @foreach ($Vehiculo as $item)
    <tr>
        <td>{{ $item->cliente->nombres }}</td>
        <td>{{ $item->marca }}</td>
        <td>{{ $item->modelo }}</td>
        <td>{{ $item->año }}</td>
        <td>{{ $item->color }}</td>
        <td>{{ $item->placa }}</td>
        <td>
            <a href="{{ route('vehiculo.consultaDetVehic', ['marcaVehiculo' => $item->marca, 'modeloVehiculo' => $item->modelo]) }}">Ver Detalle</a>
        </td>
    </tr>
    @endforeach
    </table>
</div>

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
                            <th>Nombre</th>
                            <th>Edad</th>
                            <th>Sexo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Cliente as $cliente)
                        <tr>
                            <td>{{ $cliente->nombres }}</td>
                            <td>{{ $cliente->edad }}</td>
                            <td>{{ $cliente->sexo }}</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="seleccionarCliente('{{ $cliente->id }}', '{{ $cliente->nombres }}')">Seleccionar</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    function seleccionarCliente(id, nombre) {
        document.getElementById('id').value = id;
        document.getElementById('nombre_cliente').value = nombre;
        var modal = bootstrap.Modal.getInstance(document.getElementById('clienteModal'));
        modal.hide();
    }

    // Obtener la fecha actual
    const hoy = new Date();
    // Formatear la fecha en formato YYYY-MM-DD
    const fechaFormateada = hoy.toISOString().split('T')[0];
    // Asignar la fecha formateada al input de tipo date
    document.getElementById('fecha_asignacion').value = fechaFormateada;
</script>
</body>
</html>
y