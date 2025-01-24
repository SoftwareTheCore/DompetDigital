<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MahasiswaController;

Route::get('/', function () {
    return view('welcome');
});

// ROUTE LOGIN
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});


// ROUTE LOGOUT (dengan middleware auth)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ROUTE ADMIN (dengan middleware auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::put('/admin/user/{id}', [AdminController::class, 'updateUser'])->name('admin.updateUser');
    Route::delete('/admin/user/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
});


// ROUTE MAHASISWA
Route::middleware(['auth'])->group(function () {
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.dashboard');
    Route::post('/mahasiswa/topup', [MahasiswaController::class, 'topUp'])->name('mahasiswa.topup');
    Route::post('/mahasiswa/transfer', [MahasiswaController::class, 'transfer'])->name('mahasiswa.transfer');
    Route::post('/mahasiswa/withdraw', [MahasiswaController::class, 'withdraw'])->name('mahasiswa.withdraw');
});