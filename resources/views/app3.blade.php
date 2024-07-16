<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taller Servimag</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body.dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .dark-mode .navbar {
            background-color: #343a40;
        }
        .dark-mode .table {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-tools me-2"></i> Taller Servimag</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/users') }}"><i class="fas fa-users me-1"></i>Gestionar Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/trabajadors') }}"><i class="fas fa-user-tie me-1"></i>Gestionar Trabajadores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/servicios') }}"><i class="fas fa-concierge-bell me-1"></i>Gestionar Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/GestionarVehiculos') }}"><i class="fas fa-car me-1"></i>Gestionar Vehiculos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/detalle') }}"><i class="fas fa-list-alt me-1"></i>Gestionar Detalles Vehiculo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/reserva') }}"><i class="fas fa-calendar-check me-1"></i>Gestionar Reservas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/promocion') }}"><i class="fas fa-calendar-check me-1"></i>Gestionar Promociones</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <button class="btn btn-outline-light" id="darkModeToggle"><i class="fas fa-moon"></i> Modo Oscuro</button>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Check the localStorage for dark mode preference
            if (localStorage.getItem('dark-mode') === 'true') {
                document.body.classList.add('dark-mode');
                document.getElementById('darkModeToggle').innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
            }

            // Toggle dark mode
            document.getElementById('darkModeToggle').addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                let isDarkMode = document.body.classList.contains('dark-mode');
                localStorage.setItem('dark-mode', isDarkMode);
                this.innerHTML = isDarkMode ? '<i class="fas fa-sun"></i> Modo Claro' : '<i class="fas fa-moon"></i> Modo Oscuro';
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

