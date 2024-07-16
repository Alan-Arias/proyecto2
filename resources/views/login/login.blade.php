<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1e4673; /* Fondo azul oscuro */
            padding: 20px;
            overflow: hidden; /* Ocultar el desbordamiento para evitar barras de desplazamiento */
            position: relative; /* Posición relativa para elementos posicionados absolutamente */
        }

        /* Estilo del patrón de puntos */
        .dot-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(circle, #ffffff 1%, transparent 1%);
            background-size: 20px 20px; /* Tamaño del patrón */
            z-index: -1; /* Colocar detrás de otros elementos */
            top: 0;
            left: 0;
            animation: moveDots 10s linear infinite; /* Animación de movimiento */
        }

        /* Animación de movimiento de los puntos */
        @keyframes moveDots {
            from {
                background-position: 0 0; /* Posición inicial */
            }
            to {
                background-position: 20px 20px; /* Posición final (mismo tamaño que el patrón) */
            }
        }

        .login-form {
            max-width: 350px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            position: relative; /* Asegurar que esté sobre el fondo */
            z-index: 1; /* Asegurar que esté sobre el fondo */
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="dot-pattern"></div> <!-- Patrón de puntos -->
    <div class="login-form">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="/login">
            @csrf
            <div class="form-group">
                <label for="user">Usuario:</label>
                <input type="text" class="form-control" id="user" name="user" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
            </div>
        </form>
        @if (session('error'))
            <div class="alert alert-danger mt-3">
                <p>{{ session('error') }}</p>
            </div>
        @endif
    </div>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
