<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BannerController;

Route::get('/admin/layout', [UserController::class, 'index'])->middleware('auth');

/* Authenticate */
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login_get');
Route::post('/login', [AuthController::class, 'login'])->name('login_post');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register_get');
Route::post('/register', [AuthController::class, 'register'])->name('register_post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout_post');

/* User */
Route::get('/user', [UserController::class, 'index'])->name('user_get');
Route::patch('/user/{id}/toggle', [UserController::class, 'changeStatus'])->name('admin.change_status_user');

/* Banner */
Route::get('/banner', [BannerController::class, 'index'])->name('banner_get');