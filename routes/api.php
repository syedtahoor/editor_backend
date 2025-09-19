<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/contact', [ContactController::class, 'send']);

// Payment routes
Route::get('/stripe/key', [PaymentController::class, 'getPublishableKey']);
Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/confirm-payment', [PaymentController::class, 'confirmPayment']);


Route::post('/export-video', [ExportController::class, 'exportVideo']);
Route::get('/export-status/{jobId}', [ExportController::class, 'checkExportStatus']);
Route::get('/download-export/{filename}', [ExportController::class, 'downloadExport']);