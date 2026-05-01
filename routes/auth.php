<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;



Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest', 'throttle:5,1']);


Route::middleware('auth')->group(function () {

    Route::get('password/change', [PasswordController::class, 'editForcedChange'])
        ->middleware('account.active')
        ->name('password.change.edit');

    Route::put('password/change', [PasswordController::class, 'updateForcedChange'])
        ->middleware('account.active')
        ->name('password.change.update');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);


    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
