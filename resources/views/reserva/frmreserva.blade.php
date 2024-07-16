<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49f6844fc6.js" crossorigin="anonymous"></script>
</head>
<body>
extends('app3')

@section('content')

<div class="container mt-4">
    <h2>Reservas del Servicio</h2>
    
    <div class="container">
    <div class="form-container">
        <form action="{{ url('/registrarReserva') }}" method="POST" class="forms">
            @csrf
            @if(!empty($reserva))
                @method('PUT') <!-- Método HTTP para actualizar -->
            @endif
            
            <div>
                <h2>Hacer una Reserva</h2>
                <div class="mb-3">
                    <label for="id_servicio" class="form-label">Servicio</label>
                    <select class="form-control" id="id_servicio" name="id_servicio">
                        @foreach($servicios as $servicio)
                            <option value="{{ $servicio->id }}" {{ (!empty($reserva) && $reserva->id_servicio == $servicio->id) ? 'selected' : '' }}>{{ $servicio->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="fecha_reserva" class="form-label">Fecha de la Reserva</label>
                    <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva" value="{{ !empty($reserva) ? $reserva->fecha_reserva : '' }}">
                </div>
                <div class="mb-3">
                    <label for="id_cliente" class="form-label">Cliente</label>
                    <select class="form-control" id="id_cliente" name="id_cliente">
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ (!empty($reserva) && $reserva->id_cliente == $cliente->id) ? 'selected' : '' }}>{{ $cliente->nombres }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="detalle" class="form-label">Detalle el Problema</label>
                    <textarea class="form-control" id="detalle" name="detalle">{{ !empty($reserva) ? $reserva->detalle : '' }}</textarea>
                </div>
            </div>
            <div style="text-align: center;">
                <button type="submit" class="btn btn-primary">Guardar Datos</button>
            </div>
        </form>
        
        @if(!empty($reserva))
            <!-- Si existe una reserva, mostrar el botón de eliminar -->
            <form action="{{ url ('/eliminarReserva/' . $reserva->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger mt-3">Eliminar Reserva</button>
            </form>
        @endif
    </div>
</div>



    <div class="row mt-4">
        <div class="col">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Detalle de la Reserva</th>
                        <th>Fecha de la Reserva</th>
                        <th>Cliente</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($Reserva as $item)
                    
                    <tr>
                        <td>{{ $item->detalle }}</td>                        
                        <td>{{ $item->fecha_reserva }}</td>
                        <td>{{ $item->cliente->nombres }}</td>                    
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@endsection
</body>
</html>
