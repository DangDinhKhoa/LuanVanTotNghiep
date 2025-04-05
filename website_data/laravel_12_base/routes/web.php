<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Http;


/* Authenticate */
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login_get');
Route::post('/login', [AuthController::class, 'login'])->name('login_post');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register_get');
Route::post('/register', [AuthController::class, 'register'])->name('register_post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout_post');

/* User */
Route::get('/user/layout', [App\Http\Controllers\User\UserController::class, 'index'])->middleware('auth');

/* Admin */
Route::get('/admin/layout', [DashboardController::class, 'index'])->middleware('auth');
Route::get('/admin/get-dashboard', [DashboardController::class, 'index'])->name('dashboard_get');
Route::get('/admin/get-table-user', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('user_table_get');
Route::patch('/admin/change-user-status/{id}/', [App\Http\Controllers\Admin\UserController::class, 'changeStatus'])->name('admin.change_status_user');
Route::get('/admin/get-info-admin/{id}', [App\Http\Controllers\Admin\AdminController::class, 'getInfo'])->name('info_admin_get');
Route::patch('/admin/update-info-admin/{id}', [App\Http\Controllers\Admin\AdminController::class, 'updateInfo'])->name('info_admin_update');
Route::get('/admin/get-table-admin', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin_table_get');
Route::patch('/admin/change-admin-status/{id}/', [App\Http\Controllers\Admin\AdminController::class, 'changeStatus'])->name('admin.change_status_admin');
Route::get('/admin/add-admin', [App\Http\Controllers\Admin\AdminController::class, 'showAddAdminForm'])->name('add_admin_get');
Route::post('/admin/add-admin', [App\Http\Controllers\Admin\AdminController::class, 'addAdmin'])->name('add_admin_post');


/* Banner */
Route::get('/banner', [BannerController::class, 'index'])->name('banner_get');
Route::get('test-api', function () {
    try {
        $response = Http::timeout(5)->get('http://127.0.0.1:8000/generate/layout-banner');

        if ($response->successful()) {

            $text = $response->json();

            dd($text);
        } else {
            return ['error' => $response->status()];
        }
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
});