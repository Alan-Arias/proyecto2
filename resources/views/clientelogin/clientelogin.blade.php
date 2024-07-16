<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido Cliente - Taller Servimag</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49f6844fc6.js" crossorigin="anonymous"></script>
    <style>
        /* Estilos para las tarjetas de servicios */
        .card {
            border: none;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px); /* Efecto de levantamiento al pasar el ratón */
        }

        .card-img-top {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card-body {
            padding: 1.25rem;
            background-color: #f8f9fa; /* Color de fondo del cuerpo de la tarjeta */
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #007bff; /* Color azul para el título */
        }

        .card-text {
            font-size: 0.95rem;
            color: #555; /* Color del texto de la tarjeta */
        }
    </style>
</head>
<body>
@extends('app')

@section('title', 'Inicio')

@section('content')

<div class="container mt-4">
    <div class="jumbotron custom-jumbotron">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="display-4 text-primary">Bienvenido a Taller Servimag</h2>
                <p class="lead">¡Confía en nosotros para el cuidado y mantenimiento de tu vehículo! Nuestros servicios incluyen:</p>
                <ul class="lead">
                    <li>Reparaciones especializadas</li>
                    <li>Mantenimiento preventivo</li>
                    <li>Inspección técnica</li>
                    <li>Y mucho más</li>
                </ul>
            </div>
            <div class="col-md-6 text-center">
                <img src="https://lh3.googleusercontent.com/pw/AP1GczN3GT_C9aPavEB1AkFIQb5I7mvo6NCyT_b-tcUiL0sk3AyG_MTKhQ_Wa667F_i0GQL0Ggaha-zZbnJihT9UDTWPSFqve1T27NXEja9tbnWeg7wpdQmyOWJNYbAunFL7BZZhJ4wjKUxMfDYyTj_WPG6r9w=w859-h620-s-no?authuser=0" class="img-fluid rounded-circle" alt="Imagen representativa del taller">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center mb-4">Servicios Destacados</h3>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <img src="https://img.youtube.com/vi/-ZRdY96-V9U/sddefault.jpg" class="card-img-top" alt="Revisión de Bujes">
                <div class="card-body">
                    <h5 class="card-title">Revisión de Bujes</h5>
                    <p class="card-text">Esto lo realiza el mecánico para comprobar los niveles de aceite del motor, refrigerante, líquido de frenos, etc.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <img src="https://img.freepik.com/foto-gratis/cerrar-manos-mecanico-irreconocible-haciendo-servicio-mantenimiento-automoviles_146671-19691.jpg?t=st=1720761053~exp=1720761653~hmac=8887f2ab4a513ebfa6ef3489a44047b9a34da0983cdedacb59856f1c85cf47c8" class="card-img-top" alt="Reparación del Motor">
                <div class="card-body">
                    <h5 class="card-title">Reparación del Motor</h5>
                    <p class="card-text">Reparación y revisión del motor.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <img src="https://www.infotaller.tv/2020/10/06/electromecanica/sistema-ventaja-piezas-permitir-precisos_1480961896_487352_660x372.png" class="card-img-top" alt="Reparación de Cremalleras">
                <div class="card-body">
                    <h5 class="card-title">Reparación de Cremalleras</h5>
                    <p class="card-text">Reparación y mantenimiento de motores a diesel y gasolina.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@endsection
</body>
</html>
