<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware('auth')->group(function () {
    // ... suas outras rotas (dashboard, profile, etc)
    
    Route::get('/vendas', [VendaController::class, 'index'])->name('vendas.index');
    Route::post('/vendas', [VendaController::class, 'store'])->name('vendas.store');
    Route::put('/vendas/{venda}', [VendaController::class, 'update'])->name('vendas.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});

// Dentro do grupo auth