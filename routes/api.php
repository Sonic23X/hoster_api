<?php

use App\Http\Controllers\Api\v1\PropertyController;
use App\Http\Controllers\Api\V1\ServicesController;
use App\Http\Controllers\Api\v1\UsersController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Contracts\Role;

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

    Route::get('me/properties', [PropertyController::class, 'myProperties'])->name('myProperties');
    //Route::get('me/users', [PropertyController::class, 'myProperties'])->name('myProperties');

    Route::get('users/combo/{option?}', [UsersController::class, 'usersCombo'])->name('users.combo');
    Route::post('users/assignProperty', [UsersController::class, 'assignPropertyToStockholder'])->name('users.assignPropertyToStockholder');
    Route::resource('users', UsersController::class);

    Route::resource('services', ServicesController::class);
});
