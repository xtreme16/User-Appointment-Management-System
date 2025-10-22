<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect('/login');
});

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');