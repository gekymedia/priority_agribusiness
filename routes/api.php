<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FarmApiController;
use App\Http\Controllers\Api\FinancialApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\PriorityBankWebhookController;

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

// API Routes for Priority Bank Integration
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    // User/Farmer Information
    Route::get('/farmer/profile', [UserApiController::class, 'profile']);
    Route::get('/farmer/business-info', [UserApiController::class, 'businessInfo']);

    // Farm Information
    Route::get('/farms', [FarmApiController::class, 'index']);
    Route::get('/farms/{farm}', [FarmApiController::class, 'show']);

    // Financial Data
    Route::get('/financial/summary', [FinancialApiController::class, 'summary']);
    Route::get('/financial/sales', [FinancialApiController::class, 'sales']);
    Route::get('/financial/expenses', [FinancialApiController::class, 'expenses']);
    Route::get('/financial/profit-loss', [FinancialApiController::class, 'profitLoss']);

    // Production Data
    Route::get('/production/eggs', [FinancialApiController::class, 'eggProduction']);
    Route::get('/production/crops', [FinancialApiController::class, 'cropProduction']);
});

// Public API routes (if needed for bank integration)
Route::prefix('public/v1')->group(function () {
    // Add public endpoints here if needed
    Route::post('/auth/token', [UserApiController::class, 'generateToken']);
});

// -------------------------------------------------------------------------
// Priority Bank Central Finance API Webhooks
// -------------------------------------------------------------------------
// These endpoints receive finance data from Priority Bank when CEO creates
// income/expense entries and selects Priority Agriculture system.
Route::post('/webhook/finance/income', [PriorityBankWebhookController::class, 'handleIncome'])
    ->name('api.webhook.priority-bank.income');

Route::post('/webhook/finance/expense', [PriorityBankWebhookController::class, 'handleExpense'])
    ->name('api.webhook.priority-bank.expense');
