<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Usuarios</title>
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
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar-brand i {
            margin-right: 10px;
        }
        h2 {
            margin-bottom: 20px;
        }
        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-container table {
            width: 100%;
        }
        .form-container table td {
            padding: 8px;
        }
        .form-container table input,
        .form-container table select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .table-container {
            margin-top: 20px;
        }
        .search-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
@extends('app3')

@section('content')

<div class="container">
    <h2>Lista de Usuarios</h2>
    <div class="form-container">
        @include('user.formulariouser', ['Usuarios' => $Usuarios])

        <form action="{{ url('/users') }}" method="GET" class="search-form">
            @csrf
            <div class="input-group">
                <select name="criterio" id="criterio" class="form-select">
                    <option value="">Buscar por...</option>
                    <option value="id">Id</option>
                    <option value="user">User</option>
                </select>
                <input type="text" id="buscar" name="buscar" class="form-control" placeholder="Buscar">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Usuario</th>
                    <th>Contraseña</th>
                    <th>Tipo</th>
                    <th>Descripcion</th>
                    <th colspan="2">Accion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($Usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->user }}</td>
                        <td>{{ $usuario->password }}</td>
                        <td>{{ $usuario->tipo_user }}</td>
                        <td>{{ $usuario->desc_user }}</td>
                        <td>
                            <form action="{{ url('/users/eliminar/' . $usuario->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@endsection    
</body>
</html>
