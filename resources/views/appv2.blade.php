<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taller Servimag</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos generales */
        body {
            transition: background-color 0.3s ease;
        }
        .navbar-dark.bg-dark {
            background-color: #343a40 !important;
        }
        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.75);
        }
        .navbar-dark .navbar-nav .nav-link:hover {
            color: rgba(255, 255, 255, 0.95);
        }
        .navbar-text {
            color: rgba(255, 255, 255, 0.75);
        }
        .btn-secondary {
            margin-left: 15px;
        }

        /* Estilos para modo oscuro */
        body.dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .navbar-dark.bg-dark.dark-mode {
            background-color: #121212 !important;
        }
        .navbar-dark .navbar-nav .nav-link.dark-mode {
            color: rgba(255, 255, 255, 0.75);
        }
        .navbar-dark .navbar-nav .nav-link.dark-mode:hover {
            color: rgba(255, 255, 255, 0.95);
        }
        .navbar-text.dark-mode {
            color: rgba(255, 255, 255, 0.75);
        }
        .btn-secondary.dark-mode {
            background-color: #343a40;
            border-color: #343a40;
            color: #ffffff;
        }
        .btn-secondary.dark-mode:hover {
            background-color: #343a40;
            border-color: #343a40;
            color: #ffffff;
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
                    <a class="nav-link" href="{{ url('/vehiculosview') }}"><i class="fas fa-car me-1"></i> Ver Vehículos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/asignacionserview') }}"><i class="fas fa-tools me-1"></i> Asignación de Servicios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/trabjview') }}"><i class="fas fa-users me-1"></i> Trabajadores</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    @if(session('nombres') && session('cliente_id'))
                        <span class="navbar-text">Cliente: {{ session('nombres') }} (ID: {{ session('cliente_id') }})</span>
                    @elseif(session('nombres') && session('trabajador_id'))
                        <span class="navbar-text">Trabajador: {{ session('nombres') }} (ID: {{ session('trabajador_id') }})</span>
                    @else
                        <span class="navbar-text">Usuario no identificado</span>
                    @endif
                </li>
                <li class="nav-item">
                    <button id="toggle-dark-mode" class="btn btn-secondary">Modo Oscuro</button>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Contenido principal -->
<div class="container mt-5">
    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const toggleDarkMode = document.getElementById('toggle-dark-mode');
        const darkMode = localStorage.getItem('darkMode');

        // Función para activar o desactivar el modo oscuro
        const enableDarkMode = () => {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
        };

        const disableDarkMode = () => {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', null);
        };

        // Activar el modo oscuro si está guardado en localStorage
        if (darkMode === 'enabled') {
            enableDarkMode();
        }

        // Evento de clic para alternar el modo oscuro
        toggleDarkMode.addEventListener('click', () => {
            if (document.body.classList.contains('dark-mode')) {
                disableDarkMode();
            } else {
                enableDarkMode();
            }
        });
    });
</script>
</body>
</html>
