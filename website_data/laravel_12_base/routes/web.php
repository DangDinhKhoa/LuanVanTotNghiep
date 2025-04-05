<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Http;


// Authenticate
Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'showLoginForm')->name('login_get');
    Route::post('/login', 'login')->name('login_post');
    Route::get('/register', 'showRegistrationForm')->name('register_get');
    Route::post('/register', 'register')->name('register_post');
    Route::post('/logout', 'logout')->name('logout_post');
});

// User
Route::middleware('auth')->prefix('user')->group(function () {
    Route::controller(App\Http\Controllers\User\HomeController::class)->group(function () {
        Route::get('/layout','index')->name('homepage');
    });

    Route::controller(App\Http\Controllers\User\UserController::class)->group(function () {
        Route::get('/get-info-user/{id}', 'getInfo')->name('info_user_get');
        Route::post('/update-info-user/{id}', 'updateInfo')->name('info_user_update');
    });
});

// Admin
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/layout', 'index');
        Route::get('/get-dashboard', 'index')->name('dashboard_get');
    });

    Route::controller(App\Http\Controllers\Admin\UserController::class)->group(function () {
        Route::get('/get-table-user', 'index')->name('user_table_get');
        Route::patch('/change-user-status/{id}', 'changeStatus')->name('admin.change_status_user');
    });

    Route::controller(App\Http\Controllers\Admin\AdminController::class)->group(function () {
        Route::get('/get-info-admin/{id}', 'getInfo')->name('info_admin_get');
        Route::patch('/update-info-admin/{id}', 'updateInfo')->name('info_admin_update');
        Route::get('/get-table-admin', 'index')->name('admin_table_get');
        Route::patch('/change-admin-status/{id}', 'changeStatus')->name('admin.change_status_admin');
        Route::get('/add-admin', 'showAddAdminForm')->name('add_admin_get');
        Route::post('/add-admin', 'addAdmin')->name('add_admin_post');
    });
});


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