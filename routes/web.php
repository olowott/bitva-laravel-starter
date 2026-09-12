<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })
        ->middleware('verified')
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

Route::middleware(['auth', 'active', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('can:users.view')
            ->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->middleware('can:users.create')
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->middleware('can:users.create')
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->middleware('can:users.update')
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('can:users.update')
            ->name('users.update');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->middleware('can:users.delete')
            ->name('users.destroy');

        //ROLE
        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('can:roles.view')
            ->name('roles.index');

        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('can:roles.manage')
            ->name('roles.edit');

        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('can:roles.manage')
            ->name('roles.update');

        // SETTINGS
        Route::get('/settings', [SettingsController::class, 'edit'])
            ->middleware('can:settings.view')
            ->name('settings.edit');

        Route::put('/settings', [SettingsController::class, 'update'])
            ->middleware('can:settings.manage')
            ->name('settings.update');
    });

require __DIR__ . '/auth.php';
