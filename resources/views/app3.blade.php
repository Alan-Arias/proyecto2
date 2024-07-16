<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taller Servimag</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Modo oscuro */
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }
        .dark-mode .navbar {
            background-color: #343a40;
        }
        .dark-mode .table {
            background-color: #1e1e1e;
            color: #e0e0e0;
        }
        .dark-mode .table thead {
            background-color: #2c2c2c;
        }
        .dark-mode .table tbody tr {
            border-bottom: 1px solid #444;
        }
        .dark-mode .table tbody tr:nth-child(even) {
            background-color: #2a2a2a;
        }
        .dark-mode .table tbody tr:hover {
            background-color: #333;
        }
        .dark-mode .btn-outline-light {
            border-color: #e0e0e0;
            color: #e0e0e0;
        }

        /* Temas */
        body.theme-kids {
            background-color: #FFEB3B;
            color: #000;
            font-size: 18px;
        }
        body.theme-kids .navbar, body.theme-kids .navbar-nav .nav-link, body.theme-kids .btn-outline-light {
            background-color: #FBC02D;
            color: #000;
        }
        body.theme-kids .btn-outline-light {
            border-color: #000;
        }
        body.theme-kids h1, body.theme-kids h2, body.theme-kids h3 {
            color: #000;
        }
        body.theme-kids .navbar-brand {
            font-family: 'Comic Sans MS', sans-serif;
        }

        body.theme-teens {
            background-color: #00BCD4;
            color: #FFF;
            font-size: 16px;
        }
        body.theme-teens .navbar, body.theme-teens .navbar-nav .nav-link, body.theme-teens .btn-outline-light {
            background-color: #0097A7;
            color: #FFF;
        }
        body.theme-teens .btn-outline-light {
            border-color: #FFF;
        }
        body.theme-teens h1, body.theme-teens h2, body.theme-teens h3 {
            color: #FFF;
        }
        body.theme-teens .navbar-brand {
            font-family: 'Arial Black', sans-serif;
        }

        body.theme-adults {
            background-color: #3E2723;
            color: #FFF;
            font-size: 14px;
        }
        body.theme-adults .navbar, body.theme-adults .navbar-nav .nav-link, body.theme-adults .btn-outline-light {
            background-color: #1B1B1B;
            color: #FFF;
        }
        body.theme-adults .btn-outline-light {
            border-color: #FFF;
        }
        body.theme-adults h1, body.theme-adults h2, body.theme-adults h3 {
            color: #FFF;
        }
        body.theme-adults .navbar-brand {
            font-family: 'Times New Roman', serif;
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
                        <a class="nav-link" href="{{ url('/promocion') }}"><i class="fas fa-calendar-check me-1"></i>Gestionar Promocion</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <button class="btn btn-outline-light" id="darkModeToggle"><i class="fas fa-moon"></i> Modo Oscuro</button>
                    </li>
                    <li class="nav-item">
                        <select class="form-control" id="themeSelector">
                            <option value="default">Seleccionar Tema</option>
                            <option value="theme-kids">Niños</option>
                            <option value="theme-teens">Jóvenes</option>
                            <option value="theme-adults">Adultos</option>
                        </select>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
        <div class="mt-4">
            <p id="visitorCount"></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Modo oscuro
            if (localStorage.getItem('dark-mode') === 'true') {
                document.body.classList.add('dark-mode');
                document.getElementById('darkModeToggle').innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
            }

            document.getElementById('darkModeToggle').addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                let isDarkMode = document.body.classList.contains('dark-mode');
                localStorage.setItem('dark-mode', isDarkMode);
                this.innerHTML = isDarkMode ? '<i class="fas fa-sun"></i> Modo Claro' : '<i class="fas fa-moon"></i> Modo Oscuro';
            });

            // Selección de tema
            const selectedTheme = localStorage.getItem('theme');
            if (selectedTheme) {
                document.body.classList.add(selectedTheme);
                document.getElementById('themeSelector').value = selectedTheme;
            }

            document.getElementById('themeSelector').addEventListener('change', function() {
                document.body.classList.remove('theme-kids', 'theme-teens', 'theme-adults');
                const theme = this.value;
                if (theme !== 'default') {
                    document.body.classList.add(theme);
                }
                localStorage.setItem('theme', theme);
            });

            // Contador de visitas
            let visitCount = localStorage.getItem('visitCount') || 0;
            visitCount++;
            localStorage.setItem('visitCount', visitCount);
            document.getElementById('visitorCount').textContent = `Número de visitas: ${visitCount}`;
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
