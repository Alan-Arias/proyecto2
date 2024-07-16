<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Trabajadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49f6844fc6.js" crossorigin="anonymous"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
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
                    <a class="nav-link" href="/users"><i class="fas fa-user"></i> Gestionar Usuarios</a>
                </li>                
                <li class="nav-item">
                    <a class="nav-link" href="/trabajadors"><i class="fas fa-users"></i> Gestionar Trabajadores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/servicios"><i class="fas fa-cogs"></i> Gestionar Servicios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/GestionarVehiculos"><i class="fas fa-car"></i> Gestionar Vehiculos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/detalle"><i class="fas fa-info-circle"></i> Gestionar Detalles Vehiculo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/reserva"><i class="fas fa-calendar-alt"></i> Gestionar Reservas</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2>Registro de Trabajadores</h2>
    <div class="form-container">
        @if(!empty($Trabajador->id))
        @else
        <form action="/registrarTr" method="POST">
            @csrf
            <table>
                <tr>
                    <td>Carnet</td>
                    <td><input type="text" name="id" id="id" placeholder="Carnet de Identidad"></td>
                </tr>
                <tr>
                    <td>Nombre de Usuario</td>
                    <td><input type="text" id="user" name="user" placeholder="Nombre de Usuario"></td>
                </tr>
                <tr>
                    <td>Contraseña</td>
                    <td><input type="password" id="pass" name="pass"></td>
                </tr>
                <tr>
                    <td>Tipo de Usuario</td>
                    <td>
                        <select name="tipo_user" id="tipo_user">
                            <option value="2">Cliente</option>
                            <option value="1">Trabajador</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Descripcion Usuario</td>
                    <td><input type="text" id="desc_user" name="desc_user" placeholder="Descripcion del Usuario"></td>
                </tr>
                <tr>
                    <td>Nombres</td>
                    <td><input type="text" id="nombres" name="nombres"></td>
                </tr>
                <tr>
                    <td>Edad</td>
                    <td><input type="text" id="edad" name="edad"></td>
                </tr>
                <tr>
                    <td>Sexo</td>
                    <td>
                        <select name="sexo" id="sexo">
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                        </select>
                    </td>
                </tr>            
                <tr>
                    <td colspan="2" class="text-end"><input type="submit" class="btn btn-success" value="Guardar"></td>
                </tr>
            </table>
        </form>
        @if (session('agregar'))
        <div class="alert alert-success mt-3">
            <p>{{ session('agregar') }}</p>
        </div>
        @endif
        @endif
    </div>

    <div class="table-container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Contraseña</th>
                    <th>Tipo</th>
                    <th>Nombre</th>
                    <th>Edad</th>
                    <th>Sexo</th>
                    <th>Id User</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($Trabajador as $item)
                <tr>
                    <td>{{ $item->user }}</td>
                    <td>{{ $item->password }}</td>
                    <td>{{ $item->desc_user }}</td>
                    <td>{{ $item->nombres }}</td>
                    <td>{{ $item->edad }}</td>
                    <td>{{ $item->sexo }}</td>
                    <td>{{ $item->usuario_id }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
