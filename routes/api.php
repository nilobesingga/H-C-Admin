<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RequestController;

// Public Auth Routes
Route::post('/login', [AuthController::class, 'apiLogin']);

// Token verification route (no authentication required as it's used to verify tokens)
Route::post('/verify-token', [AuthController::class, 'verifyToken']);

// Protected Routes
Route::middleware('auth.api.token')->group(function () {
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

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
});
Route::group(['prefix' => 'request'], function(){
    Route::post('/personal-details', [App\Http\Controllers\DashboardController::class, 'savePersonalDetails']);
    Route::post('/company-setup', [App\Http\Controllers\CompanySetupController::class, 'store']);
    Route::post('/document', [App\Http\Controllers\RequestController::class, 'storeRequestApi']);
});

