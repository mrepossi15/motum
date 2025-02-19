<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ParkController;
use Algolia\AlgoliaSearch\Api\SearchClient;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;


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


//////ACTIVIDADES PARA DROPDOWN

Route::get('/activities', function () {
    return response()->json(Activity::select('id', 'name')->get());
});

// routes/api.php
Route::get('/trainings', [TrainingController::class, 'getTrainingsByPark']);





// 🔹 Webhook de Mercado Pago para recibir confirmaciones de pago
Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');



Route::get('/trainings', [TrainingController::class, 'getTrainingsForWeek'])->name('api.trainings');

Route::get('/nearby-parks', [ParkController::class, 'getNearbyParks']);