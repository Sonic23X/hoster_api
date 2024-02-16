<?php

use App\Http\Controllers\Api\v1\PropertyController;
use App\Http\Controllers\Api\v1\ReservationController;
use App\Http\Controllers\Api\V1\ServicesController;
use App\Http\Controllers\Api\v1\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

require __DIR__ . '/auth.php';

Route::group(['middleware' => 'auth:api'], function () {

    Route::get('properties', [PropertyController::class, 'index'])->name('index');
    Route::get('properties/{uuid}', [PropertyController::class, 'show'])->name('show');
    Route::post('properties', [PropertyController::class, 'store'])->name('store');
    Route::post('properties/{uuid}', [PropertyController::class, 'update'])->name('update');
    Route::delete('properties/{uuid}', [PropertyController::class, 'destroy'])->name('destroy');
    Route::get('properties/{uuid}/users', [PropertyController::class, 'getUsers'])->name('users');
    Route::post('properties/{uuid}/users', [PropertyController::class, 'addUser'])->name('addUser');
    Route::get('properties/{uuid}/users/available', [PropertyController::class, 'getAvailableUsers'])->name('availableUsers');
    Route::delete('properties/{uuid}/users/{userUuid}', [PropertyController::class, 'removeUser'])->name('removeUser');

    Route::get('me/properties', [UsersController::class, 'myProperties'])->name('myProperties');
    Route::get('me/reservations', [ReservationController::class, 'getMyReservations'])->name('myReservations');

    Route::get('users/combo/{option?}', [UsersController::class, 'usersCombo'])->name('users.combo');
    Route::resource('users', UsersController::class);

    Route::resource('services', ServicesController::class);

    Route::get('reservations/{uuid}', [ReservationController::class, 'getReservations'])->name('getReservations');
    Route::get('reservation/{uuid}', [ReservationController::class, 'show'])->name('show');
    Route::post('reservations', [ReservationController::class, 'store'])->name('store');
    Route::delete('reservations/{uuid}', [ReservationController::class, 'destroy'])->name('destroy');
});
