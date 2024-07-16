<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<!-- En el head de tu archivo de layout -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <title>Ejemplo - Integración</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        /* Estilos para el spinner de carga */
        .loader {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: rgba(255, 255, 255, 0.8);
            display: none; /* Ocultar por defecto */
        }
        .loader .spinner-border {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
    <style>
        .navbar-nav .nav-link {
            color: white; /* Color de texto predeterminado */
        }
        .navbar-nav .nav-link:hover {
            background-color: #343a40; /* Color de fondo al hacer hover */
            color: #FFD700; /* Color de texto al hacer hover */
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .nav-item {
            margin-right: 15px;
        }
        .nav-link {
            font-size: 1.1rem;
        }
        .form-control {
            width: 200px;
        }
    </style>
</head>

<body class="antialiased">
@include('app')

<!-- Formulario -->
<form id="primerFormulario" action="/GuardarDatos" method="post">
    @csrf
    <table>
        <tr>
            <td>Razon Social</td>
            <td><input readonly type="text" id="tcRazonSocial" name="tcRazonSocial" placeholder="Nombre del Usuario"></td>
            <td><input type="hidden" id="cliente_id" name="cliente"></td>
            <!--<td><button type="button" class="btn btn-primary mt-2" onclick="openClienteModal()">Seleccionar Cliente</button></td>-->
        </tr>
        <tr>
            <td>Correo</td>
            <td><input type="text" name="correo" placeholder="Correo Electrónico"></td>
        </tr>
        <tr>
            <td>Monto</td>
            <td><input readonly type="text" name="monto" id="monto"></td>
        </tr>
        <tr>
            <td>Servicio</td>
            <td><input readonly type="text" id="productoInput" name="pedido_detalle[producto]" ></td>
            <td><button type="button" class="btn btn-primary mt-2" onclick="openServiceModal()">Seleccionar</button></td>
        </tr>
        <tr>
            <td><input type="submit" value="Guardar" class="btn btn-primary"></td>
        </tr>
    </table>
</form>

<!-- Modal de Selección de Cliente -->
<div class="modal fade" id="clienteModal" tabindex="-1" role="dialog" aria-labelledby="clienteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clienteModalLabel">Seleccionar Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Edad</th>
                            <th>Sexo</th>
                            <th>ID Usuario</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Cliente as $cliente)
                        <tr>
                            <td>{{ $cliente->id }}</td>
                            <td>{{ $cliente->nombres }}</td>
                            <td>{{ $cliente->edad }}</td>
                            <td>{{ $cliente->sexo }}</td>
                            <td>{{ $cliente->usuario_id }}</td>
                            <td><button type="button" class="btn btn-primary" onclick="selectCliente('{{ $cliente->id }}', '{{ $cliente->nombres }}')">Seleccionar</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Selección de Servicio -->
<div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selecciona un Servicio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Costo</th>
                                <th>Duración Estimada (horas)</th>
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
                                    <td><button type="button" class="btn btn-primary" onclick="selectService('{{ $item->nombre }}', '{{ $item->costo }}')">Seleccionar</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Funciones para abrir y seleccionar en los modales
    function openClienteModal() {
        $('#clienteModal').modal('show');
    }

    function selectCliente(id, nombre) {
        document.getElementById('tcRazonSocial').value = nombre;
        document.getElementById('cliente_id').value = id;
        $('#clienteModal').modal('hide');
    }

    function openServiceModal() {
        $('#serviceModal').modal('show');
    }

    function selectService(nombreServicio, costoServicio) {
        $('#productoInput').val(nombreServicio);
        $('#monto').val(costoServicio);
        $('#serviceModal').modal('hide');
    }
</script>
<script>
    var clienteId = '{{ session('cliente_id') }}';
    var clienteNombre = '{{ session('nombres') }}';
    document.getElementById('cliente_id').value = clienteId;
    document.getElementById('tcRazonSocial').value = clienteNombre;
</script>

</body>
</html>