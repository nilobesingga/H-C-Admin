<?php

// Invoice Email Routes

use App\Http\Controllers\ZiinaWebhookController;
use Illuminate\Support\Facades\Route;

Route::controller(App\Http\Controllers\InvoiceEmailController::class)->prefix('invoice-emails')->name('invoice-emails.')->group(function() {
    Route::post('/send', 'sendInvoiceEmail')->name('send');
    Route::post('/send-existing', 'sendExistingInvoiceEmail')->name('send-existing');
    Route::post('/paymentLink', 'createPaymentIntent')->name('paymentLink');
    Route::get('/get-file/{filename}', 'getFile')->name('get-file');
});

