<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\StaffBankMiniController;

// Home Page Route
Route::get('/', function () {
    return view('home');
});

// Route for /home
Route::get('/home', function () {
    return view('home');
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

// ROUTE STAFF BANK MINI
Route::middleware(['auth', 'staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffBankMiniController::class, 'dashboard'])->name('staff.dashboard');
    Route::post('/staff/approve/{id}', [StaffBankMiniController::class, 'approve'])->name('staff.approve');
    Route::post('/staff/reject/{id}', [StaffBankMiniController::class, 'reject'])->name('staff.reject');
});
