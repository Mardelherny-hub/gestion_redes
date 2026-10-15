<?php

use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/{tenantSlug}', [WebhookController::class, 'handle'])
    ->middleware('throttle:30,1')
    ->name('api.webhook');
// Read-only aggregated evidence for the Red-Byte collector. Disabled by default.
Route::get('/internal/maintenance-evidence/v1', [\App\Http\Controllers\Api\RedByteMaintenanceEvidenceController::class, 'show'])
    ->middleware('throttle:6,1');
