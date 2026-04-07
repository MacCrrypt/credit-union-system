<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::middleware(['auth'])->group(function () {

    // Staff + Admin
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');



    // Admin only
    Route::prefix('admin')->middleware(['admin'])->group(function () {

        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/activity-logs', [UserController::class, 'activityLogs'])->name('admin.activity-logs');
    });
});





require __DIR__ . '/auth.php';
