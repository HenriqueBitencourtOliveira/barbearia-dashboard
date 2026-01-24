<?php

use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware('auth')->group(function () {
    // ... suas outras rotas (dashboard, profile, etc)
    
    Route::get('/vendas', [VendaController::class, 'index'])->name('vendas.index');
    Route::post('/vendas', [VendaController::class, 'store'])->name('vendas.store');
});

// Dentro do grupo auth