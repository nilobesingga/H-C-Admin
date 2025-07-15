<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\Settings\AdminSettingsController;
use App\Http\Controllers\Admin\Settings\AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Company\CompanyDashboardController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

//########################################### Auth #################################################
Route::view('/', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'doLogin'])->name('auth-login');
Route::get('/login/{accessToken}', [AuthController::class, 'loginByAccessToken']);

// 2FA Verification routes
Route::get('/verify', function () {
    return view('auth.verify');
})->name('verify');
Route::post('/verify', [AuthController::class, 'verify2FA'])->name('verify-2fa');

Route::middleware(['auth', 'verified'])->group(function(){
    //########################################### USER #################################################
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/account-setting', [AuthController::class, 'accountSetting'])->name('account-setting');
    // logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::group(['middleware' => ['checkModuleAccess']], function (){
        // Reports
    });
    //########################################### Company ###############################################################
    Route::group(['prefix' => 'company', 'as' => 'company.'], function(){
        Route::get('/{company?}', [CompanyDashboardController::class, 'index'])->name('index');
        Route::get('/request/{company?}', [CompanyDashboardController::class, 'getRequest'])->name('request');
        Route::get('/task/{company?}', [CompanyDashboardController::class, 'getTask'])->name('task');
        Route::get('/inbox/{company?}', [CompanyDashboardController::class, 'getInbox'])->name('inbox');
        Route::get('/payment/{company?}', [CompanyDashboardController::class, 'getPayment'])->name('payment');
        Route::get('/calendar/{company?}', [CompanyDashboardController::class, 'getCalendar'])->name('calendar');
        Route::get('/wallet/{company?}', [CompanyDashboardController::class, 'getWallet'])->name('wallet');
        Route::get('/quickchat/{company?}', [CompanyDashboardController::class, 'getQuickChat'])->name('quickchat');
        Route::get('/switch/{company}', [CompanyDashboardController::class, 'switchCompany'])->name('switch');
    });

    //########################################### ADMIN ###############################################################

    Route::group(['middleware' => ['isAdmin'], 'prefix' => 'admin', 'as' => 'admin.'], function(){
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/task', [AdminDashboardController::class, 'getTask'])->name('task');
        Route::get('/request', [AdminDashboardController::class, 'getRequest'])->name('request');
        Route::get('/inbox', [AdminDashboardController::class, 'getInbox'])->name('inbox');
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
                // Add user create route
                Route::post('/users/create', 'create');
            });
            Route::post('/user/{userId}/update-password', [AuthController::class, 'updatePassword']);
        });

        // Contacts
        Route::resource('contacts', App\Http\Controllers\ContactController::class);

        // Contact Relationships
        Route::get('/contacts/{contact}/relationships/create', [App\Http\Controllers\ContactController::class, 'createRelationship'])->name('contacts.relationships.create');
        Route::post('/contacts/{contact}/relationships', [App\Http\Controllers\ContactController::class, 'storeRelationship'])->name('contacts.relationships.store');
        Route::get('/contacts/{contact}/relationships/{relationship}/edit', [App\Http\Controllers\ContactController::class, 'editRelationship'])->name('contacts.relationships.edit');
        Route::put('/contacts/{contact}/relationships/{relationship}', [App\Http\Controllers\ContactController::class, 'updateRelationship'])->name('contacts.relationships.update');
        Route::delete('/contacts/{contact}/relationships/{relationship}', [App\Http\Controllers\ContactController::class, 'destroyRelationship'])->name('contacts.relationships.destroy');
    });
});








