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
            Route::get('/bank-monitoring', [ReportsController::class, 'getBankMonitoring'])->name('bank-monitoring');
            Route::get('/bank-accounts', [ReportsController::class, 'getBankAccounts'])->name('bank-accounts');
            Route::get('/bank-accounts', [ReportsController::class, 'getBankAccounts'])->name('bank-accounts');

            //########################################### CRESCO Reports links ###############################################################
            Route::get('/cresco-holding', [ReportsController::class, 'redirectToReports'])->name('cresco-holding');
            Route::get('/cresco-accounting', [ReportsController::class, 'redirectToReports'])->name('cresco-accounting');
            Route::get('/cresco-sage', [ReportsController::class, 'redirectToReports'])->name('cresco-sage');
            Route::get('/hensley-and-cook', [ReportsController::class, 'redirectToReports'])->name('hensley-and-cook');
            Route::get('/orchidx', [ReportsController::class, 'redirectToReports'])->name('orchidx');
            Route::get('/expense-overview', [ReportsController::class, 'redirectToReports'])->name('expense-overview');
            Route::get('/managing-director-reports', [ReportsController::class, 'redirectToReports'])->name('managing-director-reports');
            Route::get('/crm-relationships-report', [ReportsController::class, 'redirectToReports'])->name('crm-relationships-report');
            Route::get('/demo-reports', [ReportsController::class, 'redirectToReports'])->name('demo-reports');
            Route::get('/hr-reports', [ReportsController::class, 'redirectToReports'])->name('hr-reports');
            Route::get('/bank-reports', [ReportsController::class, 'redirectToReports'])->name('bank-reports');
            Route::get('/running-accounts', [ReportsController::class, 'redirectToReports'])->name('running-accounts');

        });
        // Cash Pool
        Route::group(['prefix' => 'cash-pool', 'as' => 'cash-pool.'], function(){
            Route::get('/cash-pool-report', [ReportsController::class, 'redirectToReports'])->name('cash-pool-report');
            Route::get('/cash-pool-expense-planner', [ReportsController::class, 'getExpensePlanner'])->name('cash-pool-expense-planner');
        });
    });

    // Download Cash Release Receipt
    Route::post('/cash-request/download-released-receipt', [ReportsController::class, 'downloadCashReleasedReceipt']);

    // Sync FSA / DS2
    Route::get('/sync/FSA/documents', [DocumentSyncController::class, 'syncFSADocuments'])->name('sync.FSA.documents');
    Route::get('/sync/FSA/documents/progress', [DocumentSyncController::class, 'getSyncFSADocumentsProgress'])->name('sync.FSA.documents.progress');

    // Invoice Email Routes
    Route::controller(App\Http\Controllers\InvoiceEmailController::class)->prefix('invoice-emails')->name('invoice-emails.')->group(function() {
        Route::post('/send', 'sendInvoiceEmail')->name('send');
        Route::post('/send-existing', 'sendExistingInvoiceEmail')->name('send-existing');
    });

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
            Route::post('/user/{userId}/update-password', [AuthController::class, 'updatePassword']);
        });
    });
});








