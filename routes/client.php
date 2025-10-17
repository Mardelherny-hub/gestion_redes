<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    
    // Dashboard del Cliente
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class);

    // Gestión de Jugadores (implementaremos después)
    Route::get('/players', function () {
        return 'Players Index - Coming Soon';
    })->name('players.index');

    // Gestión de Transacciones (implementaremos después)
    Route::get('/transactions', function () {
        return 'Transactions Index - Coming Soon';
    })->name('transactions.index');

    // Profile (implementaremos después)
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
});