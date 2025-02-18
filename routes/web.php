<?php

use App\Http\Controllers\Admin\ACL\ACLController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\Settings\AdminSettingsController;
use App\Http\Controllers\Admin\Settings\AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentSyncController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Middleware\IsAdminMiddleware;
use Illuminate\Support\Facades\Route;

//########################################### Auth #################################################
Route::view('/', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'doLogin'])->name('auth-login');
Route::get('/login/{accessToken}', [AuthController::class, 'loginByAccessToken']);

Route::middleware(['auth'])->group(function(){
    //########################################### USER #################################################
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::group(['middleware' => ['checkModuleAccess']], function (){
        // Reports
        Route::group(['prefix' => 'reports', 'as' => 'reports.'], function(){
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::get('/purchase-invoices', [ReportsController::class, 'getPurchaseInvoices'])->name('purchase-invoices');
            Route::get('/cash-requests', [ReportsController::class, 'getCashRequests'])->name('cash-requests');
            Route::get('/bank-transfers', [ReportsController::class, 'getBankTransfers'])->name('bank-transfers');
            Route::get('/sales-invoices', [ReportsController::class, 'getSalesInvoices'])->name('sales-invoices');
            Route::get('/proforma-invoices', [ReportsController::class, 'getProformaInvoices'])->name('proforma-invoices');
            Route::get('/bank-summary', [ReportsController::class, 'getBankSummary'])->name('bank-summary');
            Route::get('/expense-planner', [ReportsController::class, 'getExpensePlanner'])->name('expense-planner');
        });
    });

    // Sync FSA / DS2
    Route::get('/sync/FSA/documents', [DocumentSyncController::class, 'syncFSADocuments'])->name('sync.FSA.documents');
    Route::get('/sync/FSA/documents/progress', [DocumentSyncController::class, 'getSyncFSADocumentsProgress'])->name('sync.FSA.documents.progress');

    //########################################### ADMIN ###############################################################

    Route::group(['middleware' => ['isAdmin'], 'prefix' => 'admin', 'as' => 'admin.'], function(){
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        // Settings
        Route::prefix('settings')->name('settings.')->group(function (){
            Route::get('/', [AdminSettingsController::class, 'index'])->name('main');
            Route::get('/countries', [AdminSettingsController::class, 'countries'])->name('countries');
            Route::get('/categories', [AdminSettingsController::class, 'categories'])->name('categories');
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
            });
        });
    });
});








