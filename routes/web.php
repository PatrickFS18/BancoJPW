<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('welcome');
})->name('login');


Route::get('/pagamentos', [UserDashboardController::class, 'pagamentos'])->name('pagamentos');

Route::get('/transferencia', [UserDashboardController::class, 'transferencia'])->name('transferencia');

Route::get('/extrato', [UserDashboardController::class, 'extrato'])->name('extrato');

Route::get('/home', [UserDashboardController::class, 'dashboard'])->name('home');

Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::post('/login', [LoginController::class, 'login'])->name('login');
