<?php

use App\Http\Controllers\Academico\PagoFacilController;
use Illuminate\Support\Facades\Route;

Route::post('/pagofacil/callback', [PagoFacilController::class, 'callback'])
    ->name('pagofacil.callback');
