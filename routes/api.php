<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;

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

// Управление пользователями
Route::apiResource('users', UserController::class);

// Управление балансом
Route::prefix('balance')->group(function () {
    Route::get('/{email}', [BalanceController::class, 'check']);
    Route::post('/{email}/add', [BalanceController::class, 'add']);
    Route::post('/{email}/subtract', [BalanceController::class, 'subtract']);
});

// История операций
Route::get('transactions/{email}', [TransactionController::class, 'index']);
Route::get('transactions/{email}/{limit?}', [TransactionController::class, 'index']);