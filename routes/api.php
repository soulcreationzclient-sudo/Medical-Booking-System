<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\DoctorApiController;
use App\Http\Controllers\Api\PatientApiController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\ApiChatController;
use App\Http\Middleware\ApiTokenMiddleware;
use Illuminate\Support\Facades\Route;

// ── AI Chat (public) ─────────────────────────────────────────
Route::post('/chat', [ApiChatController::class, 'chat']);

// ── Public booking status lookup ─────────────────────────────
Route::get('/v1/bookings/status/{code}', [BookingApiController::class, 'statusByCode']);

// ── Public booking cancel ─────────────────────────────────────
Route::post('/v1/bookings/cancel/{code}', [BookingApiController::class, 'cancelByCode']);

// ── Auth ─────────────────────────────────────────────────────
Route::post('/v1/auth/login', [AuthApiController::class, 'login']);

// ── Webhook (Speedbots calls this) ───────────────────────────
Route::post('/v1/webhook/speedbots', [WebhookController::class, 'speedbots']);

// ── Protected v1 routes ──────────────────────────────────────
Route::middleware(ApiTokenMiddleware::class)->prefix('v1')->group(function () {

    Route::get('/auth/me', [AuthApiController::class, 'me']);

    // Bookings
    Route::get('/bookings',                  [BookingApiController::class, 'index']);
    Route::post('/bookings',                 [BookingApiController::class, 'store']);
    Route::get('/bookings/{id}',             [BookingApiController::class, 'show']);
    Route::put('/bookings/{id}/status',      [BookingApiController::class, 'updateStatus']);
    Route::post('/bookings/{id}/reschedule', [BookingApiController::class, 'reschedule']);

    // Doctors
    Route::get('/doctors',            [DoctorApiController::class, 'index']);
    Route::get('/doctors/{id}/slots', [DoctorApiController::class, 'slots']);

    // Patients
    Route::get('/patients',        [PatientApiController::class, 'index']);
    Route::get('/patients/lookup', [PatientApiController::class, 'lookup']);
    Route::get('/patients/{id}',   [PatientApiController::class, 'show']);
});