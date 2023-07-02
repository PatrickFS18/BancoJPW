<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\PaymentsController;

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

Route::post('/inserir_chave', [PaymentsController::class, 'inserirChavePix'])->name('inserir_chave');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::post('/verificar-pix', [PaymentsController::class, 'verificarPix'])->name('verificar-pix');

Route::post('/pagamento-pix', [PaymentsController::class, 'pagamentoPix'])->name('pagamento-pix');

Route::post('/transferir', [PaymentsController::class, 'transferir'])->name('transferir');
