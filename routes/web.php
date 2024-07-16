<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsumirServicioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ServicioController;

Route::get('/', function () {
    return view('login.login');
});

Route::get('/users', [UsuarioController::class, 'index']);
Route::post('/users/registrarUsuario', [UsuarioController::class, 'store']);
Route::delete('/users/eliminar/{id}', [UsuarioController::class, 'destroy']);

Route::get('/cliente','App\Http\Controllers\ClienteController@index');
Route::post('/registrar','App\Http\Controllers\ClienteController@store');
Route::get('/editar/{id}','App\Http\Controllers\ClienteController@edit');
Route::put('/modificar/{id}','App\Http\Controllers\ClienteController@update');

Route::get('/trabajadors', 'App\Http\Controllers\TrabajadorController@index');
Route::get('/trabjview', 'App\Http\Controllers\TrabajadorController@index2');
Route::post('/registrarTr', 'App\Http\Controllers\TrabajadorController@store');

Route::get('/vehiculos', 'App\Http\Controllers\VehiculoController@index');
Route::get('/vehiculosview', 'App\Http\Controllers\VehiculoController@index2');
Route::get('/GestionarVehiculos', 'App\Http\Controllers\VehiculoController@index3');
Route::post('/registrarVehiculo', 'App\Http\Controllers\VehiculoController@store');
Route::post('/GuardarVehiculo', 'App\Http\Controllers\VehiculoController@guardarVehiculos');

Route::get('/reserva', 'App\Http\Controllers\ReservaController@index');
Route::post('/registrarReserva', 'App\Http\Controllers\ReservaController@store');
Route::get('/reserva/edit/{id}', 'App\Http\Controllers\ReservaController@edit');
Route::put('/ActualizarReserva/{id}', 'App\Http\Controllers\ReservaController@update');


Route::get('/servicios', 'App\Http\Controllers\ServicioController@index');
Route::post('/registrarServicio', 'App\Http\Controllers\ServicioController@store');
Route::get('/servicio/editar/{id}', [ServicioController::class, 'edit']);
Route::put('/ActualizarServicio/{id}', [ServicioController::class, 'update']); 

Route::get('/promocion', 'App\Http\Controllers\PromocionServicio@index');
Route::post('/registrarPromocion', 'App\Http\Controllers\PromocionServicio@store');

Route::get('/detalle', 'App\Http\Controllers\VehiculoDetalleController@index');
Route::post('/registrarDetalle', 'App\Http\Controllers\VehiculoDetalleController@store');

Route::get('/asigserv', 'App\Http\Controllers\AsignacionServicioController@index');
Route::get('/asignacionserview', 'App\Http\Controllers\AsignacionServicioController@index2');
Route::post('/RegAsigServ', 'App\Http\Controllers\AsignacionServicioController@RegistrarAsignarServicio');

Route::get('/login', 'App\Http\Controllers\LoginController@showLoginForm')->name('login');
Route::post('/login', 'App\Http\Controllers\LoginController@login')->name('login');
Route::post('/logout', 'App\Http\Controllers\LoginController@logout')->name('logout');

Route::get('/consulta-detalle-vehiculo', 'App\Http\Controllers\VehiculoController@consultaDetVehic')->name('vehiculo.consultaDetVehic');


Route::get('/login', function () {
    return view('login.login');
});

Route::get('/payment', function () {
    return view('payment.payment');
});
Route::get('/clientelog', function () {
    return view('clientelogin.clientelogin');
});
Route::get('/trabajadorindex', function () {
    return view('trabajadorlogin.trabajadorlogin');
});
Route::get('/admin', function () {
    return view('admin.admin');
});



Route::get('/payment','App\Http\Controllers\ConsumirServicioController@index');
Route::post('/GuardarDatos','App\Http\Controllers\ConsumirServicioController@GuardarDatos');

Route::post('/consumirServicio', [ConsumirServicioController::class, 'RecolectarDatos']);
Route::post('/consultar', [ConsumirServicioController::class, 'ConsultarEstado']);
Route::get('/rqgenerator', [ConsumirServicioController::class, 'showRqGenerator']);
