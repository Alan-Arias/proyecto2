<!-- resources/views/layout.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Taller Servimag')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49f6844fc6.js" crossorigin="anonymous"></script>
    <style>
    /* Estilos generales */
    body.day-mode {
        background-color: #ffffff;
        color: #000000;
    }
    body.night-mode {
        background-color: #343a40;
        color: #ffffff;
    }
    .content-container {
        padding: 20px;
        margin: 0 auto;
        max-width: 800px;
    }
    .card-custom {
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }
    .night-mode .card-custom {
        background-color: #495057;
        color: #ffffff;
    }
    
    /* Estilos para formularios */
    .content-container form {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
        background-color: #ffffff; /* Color de fondo por defecto */
        color: #000000; /* Color de texto por defecto */
    }
    .night-mode .content-container form {
        background-color: #343a40; /* Color de fondo en modo oscuro */
        color: #ffffff; /* Color de texto en modo oscuro */
    }
    /* Estilo para los campos de entrada en formularios */
    .content-container form input[type="text"],
    .content-container form input[type="email"],
    .content-container form input[type="password"],
    .content-container form select,
    .content-container form textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-sizing: border-box;
        background-color: #ffffff; /* Color de fondo por defecto */
        color: #495057; /* Color de texto por defecto */
    }
    .night-mode .content-container form input[type="text"],
    .night-mode .content-container form input[type="email"],
    .night-mode .content-container form input[type="password"],
    .night-mode .content-container form select,
    .night-mode .content-container form textarea {
        background-color: #343a40; /* Color de fondo en modo oscuro */
        color: #ffffff; /* Color de texto en modo oscuro */
        border-color: #495057; /* Color de borde en modo oscuro */
    }
    /* Estilo para los botones en formularios */
    .content-container form button {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        background-color: #007bff; /* Color de fondo por defecto */
        color: #ffffff; /* Color de texto por defecto */
        cursor: pointer;
    }
    .content-container form button:hover {
        background-color: #0056b3; /* Color de fondo al pasar el ratón por defecto */
    }
    .night-mode .content-container form button {
        background-color: #343a40; /* Color de fondo en modo oscuro */
        color: #ffffff; /* Color de texto en modo oscuro */
    }
    .night-mode .content-container form button:hover {
        background-color: #495057; /* Color de fondo al pasar el ratón en modo oscuro */
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
                        <a class="nav-link" href="/vehiculos"><i class="fas fa-car me-1"></i> Reservas y Vehículos</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    @if (session('nombres'))
                        <li class="nav-item">
                            <span class="nav-link"><i class="fas fa-user me-1"></i>{{ session('nombres') }}</span>
                        </li>
                        @if (session('cliente_id'))
                            <li class="nav-item">
                                <span class="nav-link"><i class="fas fa-id-card me-1"></i>Cliente ID: {{ session('cliente_id') }}</span>
                            </li>
                        @endif
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link"><i class="fas fa-sign-out-alt me-1"></i>Cerrar sesión</button>
                            </form>
                        </li>
                    @endif
                    <li class="nav-item">
                        <button id="toggleTheme" class="btn btn-link nav-link"><i class="fas fa-adjust me-1"></i> Cambiar Tono</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4 content-container">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Función para cargar el tema desde el almacenamiento local
        function loadTheme() {
            const theme = localStorage.getItem('theme');
            if (theme) {
                document.body.classList.add(theme);
            } else {
                document.body.classList.add('day-mode');
            }
        }

        // Función para cambiar el tema y guardar la preferencia en el almacenamiento local
        function toggleTheme() {
            if (document.body.classList.contains('day-mode')) {
                document.body.classList.remove('day-mode');
                document.body.classList.add('night-mode');
                localStorage.setItem('theme', 'night-mode');
            } else {
                document.body.classList.remove('night-mode');
                document.body.classList.add('day-mode');
                localStorage.setItem('theme', 'day-mode');
            }
        }

        // Cargar el tema al cargar la página
        document.addEventListener('DOMContentLoaded', loadTheme);

        // Añadir el evento al botón de cambio de tema
        document.getElementById('toggleTheme').addEventListener('click', toggleTheme);
    </script>
</body>
</html>
