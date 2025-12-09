<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Webhook Routes (for receiving notifications from external sites) - No auth required
Route::post('/webhook/receive-transaction', [\App\Http\Controllers\WebhookController::class, 'receiveTransaction'])->name('webhook.receive-transaction');
Route::post('/webhook/verify', [\App\Http\Controllers\WebhookController::class, 'verifyWebhook'])->name('webhook.verify');
Route::get('/webhook/test', [\App\Http\Controllers\WebhookController::class, 'testWebhook'])->name('webhook.test');

// PayVibe Webhook Route (no auth required - PayVibe will call this)
Route::post('/webhook/payvibe', [\App\Http\Controllers\PayVibeWebhookController::class, 'handleWebhook'])->name('api.webhook.payvibe');

// XtraPay Virtual Account API Routes (for businesses to request virtual accounts)
Route::post('/v1/virtual-accounts/request', [\App\Http\Controllers\Api\PayVibeApiController::class, 'requestVirtualAccount'])->name('api.virtual-accounts.request');
Route::post('/v1/virtual-accounts/check-status', [\App\Http\Controllers\Api\PayVibeApiController::class, 'checkPaymentStatus'])->name('api.virtual-accounts.check-status'); 