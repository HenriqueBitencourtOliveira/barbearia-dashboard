<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// 1. Redireciona a raiz para a tela de login gerada pelo Auth
Route::redirect('/', '/login');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// 2. Grupo de rotas protegidas
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Vendas
    Route::resource('vendas', VendaController::class)->only([
        'index',
        'create',
        'store',
        'update'
    ]);

    // Produtos 
    Route::resource('produtos', ProdutoController::class)->only([
        'create',
        'store',
        'update',
        'destroy'
    ]);

    Route::post('/vendas/{id}/estornar', [VendaController::class, 'estornar'])->name('vendas.estornar');
});
