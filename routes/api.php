<?php

use App\Http\Controllers\Admin\Settings\AdminUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ChangeRequestController;
use App\Http\Controllers\CompanyController;

// Public Authentication Routes
Route::post('/api-login', [AuthController::class, 'apiLogin']);
Route::post('/verify-token', [AuthController::class, 'verifyToken']);

// Protected Routes
Route::middleware(['auth.api.token','admin'])->group(function () {
    // Dashboard Route
    Route::get('/dashboard', [AdminDashboardController::class, 'apiIndex']);

    // Change Request Routes
    Route::post('/change-requests/{id}/approve', [ChangeRequestController::class, 'approve']);
    Route::post('/change-requests/{id}/reject', [ChangeRequestController::class, 'reject']);

    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

    // Company
    Route::controller(CompanyController::class)->group(function(){
        // Route::get('/company', 'getCompany');
        // Route::post('/company', 'storeCompany');
        // Route::post('/company/update/{id}', 'updateCompany');
        // Route::delete('/company/{id}', 'deleteCompany');
        Route::get('/company-list', 'getCompany');
    });
    // Task Routes
    Route::controller(TaskController::class)->group(function(){
        Route::post('/tasks', 'store');
        Route::get('/tasks/list', 'getList');
        Route::post('tasks/comments/{id}', 'storeComment');
        Route::get('/tasks/{id}', 'getTaskDetails');
    });

    // Admin Token Management Routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::post('/tokens', [AuthController::class, 'generateAdminToken']);
        Route::get('/tokens', [AuthController::class, 'listAdminTokens']);
        Route::delete('/tokens/{tokenId}', [AuthController::class, 'revokeAdminToken']);
    });

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/account/update', [AuthController::class, 'updateAccount']);
    Route::post('/syncContact', [ContactController::class, 'syncContact']);
    Route::post('/company/bank-accounts', [ContactController::class, 'storeBankAccounts']);
    Route::post('/company/document', [ContactController::class, 'uploadCompanyDocument']);
    Route::post('/contact', [ContactController::class, 'storeContact']);
    Route::get('/contact/{contactId}/companies', [ContactController::class, 'getContactCompany']);
// User Management Routes
    Route::controller(AdminUserController::class)->group(function(){
        Route::get('/users', 'index')->name('users');
        Route::get('/users/list', 'getUsers');
        Route::post('/users/get-data', 'getData');
        Route::get('/user/{id}', 'edit');
        Route::post('/user/save/{userId}', 'save');
        Route::post('/users/create', 'create');
        Route::post('/user/{userId}/update-password', [AuthController::class, 'updatePassword']);
    });

    // Request Routes
    Route::controller(RequestController::class)->group(function(){
        Route::post('/requests/{id}', 'updateRequestStatus');
        Route::get('/requests', 'getRequest');
    });
});

