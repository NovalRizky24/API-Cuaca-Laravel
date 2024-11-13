<?php

use App\Http\Controllers\CuacaController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('cuaca'); 
});


Route::get('/apicuaca', [CuacaController::class, 'index']);
