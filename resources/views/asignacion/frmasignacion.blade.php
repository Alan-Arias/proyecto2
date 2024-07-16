<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignacion de Servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49f6844fc6.js" crossorigin="anonymous"></script>
</head>
<body>
    <h2>Asignar Servicios a Trabajadores</h2>
    <br>
    @if(!empty($AsignacionServicio->id))
    @else
    <form action="/RegAsigServ" method="POST">
    @csrf
        <table>
            <tr>
                <td>Fecha Servicio</td>
                <td><input type="date" name="fecha_servicio" id="fecha_servicio"></td>
            </tr>
            <tr>
                <td>Estado del Servicio</td>
                <td>
                    <select name="estado" id="estado">
                        <option value="Disponible">Servicio Normal</option>
                        <option value="No Disponible">Servicio No Disponible</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Selecciona un Trabajdor</td>
                    <td>
                        <select name="id_trabajador" id="id_trabajador">
                        @foreach ($Trabajador as $tr)
                            <option value="{{ $tr->id }}">{{ $tr->nombres }}</option>
                        @endforeach
                        </select>
                    </td>
                </tr>
            </tr>
            <tr>
                <td>Selecciona un Servicio</td>
                    <td>
                        <select name="servicio_id" id="servicio_id">
                        @foreach ($Servicio as $serv)
                            <option value="{{ $serv->id }}">{{ $serv->nombre }}</option>
                        @endforeach
                        </select>
                    </td>
                </td>
            </tr>
            <tr>
                <td><input type="submit" class="btn btn-success" value="Guardar"></td>
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
            <th>Fecha Asignacion</th>
            <th>Estado</th>
            <th>Trabajador</th>
            <th>Servicio</th>
        </tr>
        @foreach ($AsignacionServicio as $item)
        <tr>
            <td>{{ $item ->id }}</td>
            <td>{{ $item ->fecha_asignacion }}</td>
            <td>{{ $item ->estado }}</td>
            <td>{{ $item ->trabajador->nombres }}</td>
            <td>{{ $item ->servicio->nombre }}</td>
        </tr>
        @endforeach
    </table>
    <!--script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!--script -->

</body>
</html>