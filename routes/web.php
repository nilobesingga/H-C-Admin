<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\Settings\AdminSettingsController;
use App\Http\Controllers\Admin\Settings\AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Company\CompanyDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

//########################################### Auth #################################################
Route::view('/', 'auth.login')->name('login');
// Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'doLogin'])->name('auth-login');
Route::get('/login/{accessToken}', [AuthController::class, 'loginByAccessToken']);

// 2FA Verification routes
Route::get('/verify', function () {
    return view('auth.verify');
})->name('verify');
Route::post('/verify', [AuthController::class, 'verify2FA'])->name('verify-2fa');

Route::middleware(['auth', 'verified'])->group(function(){
    // logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    //########################################### ADMIN ###############################################################
    Route::group(['middleware' => ['isAdmin']], function(){
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/task', [AdminDashboardController::class, 'getTask'])->name('task');
        Route::get('/document-request', [AdminDashboardController::class, 'getDocumentRequest'])->name('document-request');
        Route::get('/request', [AdminDashboardController::class, 'getRequest'])->name('request');
        Route::get('/setup-request', [AdminDashboardController::class, 'getSetupRequest'])->name('setup-request');
        Route::get('/data-request', [AdminDashboardController::class, 'getDataRequest'])->name('data-request');
        Route::get('/change-request', [AdminDashboardController::class, 'getChangeRequest'])->name('change-request');
        Route::get('/inbox', [AdminDashboardController::class, 'getInbox'])->name('inbox');
        Route::get('/chat', [AdminDashboardController::class, 'getChat'])->name('chat');
        Route::get('/user-profile', [AdminDashboardController::class, 'profile'])->name('user-profile');
        // Settings
        Route::prefix('settings')->name('settings.')->group(function (){
            Route::get('/', [AdminSettingsController::class, 'index'])->name('main');
            Route::get('/countries', [AdminSettingsController::class, 'countries'])->name('countries');
            Route::get('/companies', [AdminSettingsController::class, 'companies'])->name('companies');
            Route::get('/modules', [AdminSettingsController::class, 'modules'])->name('modules');
            Route::post('/modules/get-data', [AdminSettingsController::class, 'moduleGetData']);
            Route::post('/modules/update', [AdminSettingsController::class, 'modulesOrderUpdate']);
            Route::get('/bitrix-sage-mapping', [AdminSettingsController::class, 'bitrixSageMapping'])->name('bitrix-sage-mapping');
            Route::post('/bitrix-sage-mapping/get-data/{id?}', [AdminSettingsController::class, 'bitrixSageMappingGetData']);
            Route::post('/bitrix-sage-mapping/save', [AdminSettingsController::class, 'bitrixSageMappingSave']);
            //  Users
            Route::controller(AdminUserController::class)->group(function(){
                Route::get('/users', 'index')->name('users');
                Route::post('/users/get-data', 'getData');
                Route::get('/user/{id}', 'edit');
                Route::post('/user/save/{userId}', 'save');
                Route::post('/users/create', 'create');
                Route::post('/user/{userId}/update-password', [AuthController::class, 'updatePassword']);
            });
            Route::post('/user/{userId}/update-password', [AuthController::class, 'updatePassword']);
        });
    });
});








