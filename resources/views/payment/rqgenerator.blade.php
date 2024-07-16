<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ejemplo - Integración</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body class="antialiased">
@include('app')
<body class="antialiased">
    <!-- Vista /rqgenerator -->
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-6 col-md-6 col-12 text-center">
            <h3>PagoFacil QR y Tigo Money</h3>                
            <div class="card">
                <form class="form-card" action="/consumirServicio" method="POST" target="QrImage" id="segundoFormulario">
                    @csrf
                    @php
                        $formData = session('formData');
                    @endphp
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Razon Social</label>
                            <input readonly type="text" name="tcRazonSocial" value="{{ $formData['tcRazonSocial'] ?? old('tcRazonSocial') }}" placeholder="Nombre del Usuario">
                        </div>
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">CI/NIT</label>
                            <input type="text" name="tcCiNit" value="{{ old('tcCiNit') }}" placeholder="Número de CI/NIT">
                        </div>
                    </div>
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Celular</label>
                            <input type="text" name="tnTelefono" id="tnTelefono" value="{{ old('tnTelefono') }}" placeholder="Número de Teléfono">
                        </div>
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Correo</label>
                            <input readonly type="text" name="tcCorreo" value="{{ $formData['tcCorreo'] ?? old('tcCorreo') }}" placeholder="Correo Electrónico">
                        </div>
                    </div>
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Monto</label>
                            <input readonly type="text" name="tnMonto" id="input1" value="{{ $formData['tnMonto'] ?? old('tnMonto') }}" placeholder="Costo Total">
                        </div>
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Tipo de Servicio</label>
                            <select name="tnTipoServicio" class="form-control">
                                <option value="1" {{ isset($formData['tnTipoServicio']) && $formData['tnTipoServicio'] == '1' ? 'selected' : '' }}>Servicio QR</option>
                                <option value="2" {{ isset($formData['tnTipoServicio']) && $formData['tnTipoServicio'] == '2' ? 'selected' : '' }}>Tigo Money</option>
                            </select>
                        </div>
                    </div>
                    <!-- Resto del formulario... -->

                    <div class="row justify-content-end">
                        <div class="form-group col-sm-6">
                            <button type="submit" class="btn-block btn-primary">Generar</button>
                        </div>
                    </div>
                </form>
                @if (session('agregar'))
                <div class="alert alert-success mt-3">
                    <p>{{ session('agregar') }}</p>
                </div>
                @endif
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-12 py-5">
            <div class="row d-flex justify-content-center">
                <iframe name="QrImage" style="width: 100%; height: 495px;"></iframe>
            </div>
        </div>
    </div>
</div>
   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('servicio').defaultValue = 'Servicos Varios';        
        document.getElementById('cantidad').defaultValue = '1';
        document.getElementById('desc').defaultValue = '0';
        const randomValue = Math.floor(Math.random() * 99999) + 1;        
        document.getElementById('numeroAleatorio').value = randomValue;
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input1 = document.getElementById('input1');
        const input2 = document.getElementById('input2');
        const input3 = document.getElementById('input3');

        // Función para sincronizar valores
        function syncValues() {
            const value = input1.value;
            input2.value = value;
            input3.value = value;
        }

        // Escuchar el evento de cambio en el primer input
        input1.addEventListener('input', syncValues);
    });
</script>
</body>
</html>