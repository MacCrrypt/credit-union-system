<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::middleware(['auth', 'account.active', 'password.current'])->group(function () {

    // Shared authenticated routes cover signed-in branch users. Central admin
    // still passes through auth, but controller checks keep member-card access
    // at branch level only.
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{member}/signature', [MemberController::class, 'signature'])->name('members.signature.show');


    // Admin route group covers central admin and branch admin responsibilities.
    Route::prefix('admin')->middleware(['admin'])->group(function () {

        Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::get('/members/{member}/delete', [MemberController::class, 'confirmDelete'])->name('members.delete.preview');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
        Route::get('/reports/members/export', [MemberController::class, 'export'])->name('admin.reports.members.export');

        // Branch management is central-admin-only at the controller level.
        Route::get('/branches', [BranchController::class, 'index'])->name('admin.branches.index');
        Route::get('/branches/create', [BranchController::class, 'create'])->name('admin.branches.create');
        Route::post('/branches', [BranchController::class, 'store'])->name('admin.branches.store');

        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{managedUser}/password', [UserController::class, 'editPassword'])->name('admin.users.password.edit');
        Route::put('/users/{managedUser}/password', [UserController::class, 'updatePassword'])->name('admin.users.password.update');
        Route::get('/users/{managedUser}/password/confirmed', [UserController::class, 'confirmPassword'])->name('admin.users.password.confirm');
        Route::get('/users/{managedUser}/status', [UserController::class, 'editStatus'])->name('admin.users.status.edit');
        Route::patch('/users/{managedUser}/status', [UserController::class, 'updateStatus'])->name('admin.users.status.update');
        Route::get('/activity-logs', [UserController::class, 'activityLogs'])->name('admin.activity-logs');
        Route::get('/activity-logs/export', [UserController::class, 'exportActivityLogs'])->name('admin.activity-logs.export');
    });
});





require __DIR__ . '/auth.php';
