<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Http\Controllers\AuthController;
use App\Modules\Users\Http\Controllers\UserController;
use App\Modules\Organizations\Http\Controllers\OrganizationController;
use App\Modules\Affiliations\Http\Controllers\AffiliationController;
use App\Modules\Insurance\Http\Controllers\PackageController;
use App\Modules\Insurance\Http\Controllers\PartnerOfferingController;
use App\Modules\Insurance\Http\Controllers\ContractController;
use App\Modules\Insurance\Http\Controllers\InsuranceRequestController;
use App\Modules\Claims\Http\Controllers\ClaimController;
use App\Modules\Claims\Http\Controllers\ClaimResponseController;
use App\Modules\Wallet\Http\Controllers\WalletController;
use App\Modules\Billing\Http\Controllers\InvoiceController;
use App\Modules\Accounting\Http\Controllers\AccountingPeriodController;
use App\Modules\Accounting\Http\Controllers\ReconciliationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| SPA (Vue) will use Sanctum cookie auth:
| 1) GET /sanctum/csrf-cookie (web middleware)
| 2) POST /api/v1/auth/login
| 3) Subsequent requests with credentials to /api/v1/*
|
*/

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);

        // Users
        Route::apiResource('users', UserController::class);

        // Organizations
        Route::apiResource('organizations', OrganizationController::class);

        // Affiliations
        Route::post('affiliations', [AffiliationController::class, 'store']);
        Route::get('users/{user}/affiliations', [AffiliationController::class, 'listForUser']);

        // Offers / Packages / Contracts
        Route::apiResource('packages', PackageController::class);
        Route::apiResource('partner-offerings', PartnerOfferingController::class);
        Route::apiResource('contracts', ContractController::class);

        // Claims
        Route::apiResource('claims', ClaimController::class);
        Route::post('claims/{claim}/approve', [ClaimController::class, 'approve']);
        Route::post('claims/{claim}/reject', [ClaimController::class, 'reject']);
        Route::post('claims/{claim}/close', [ClaimController::class, 'close']);
        Route::get('claims/{claim}/responses', [ClaimResponseController::class, 'index']);
        Route::post('claims/{claim}/responses', [ClaimResponseController::class, 'store']);

        // Insurance requests
        Route::get('insurance/requests', [InsuranceRequestController::class, 'index']);
        Route::get('insurance/requests/{insuranceRequest}', [InsuranceRequestController::class, 'show']);
        Route::patch('insurance/requests/{insuranceRequest}', [InsuranceRequestController::class, 'update']);

        // Wallet
        Route::get('wallet', [WalletController::class, 'show']);
        Route::post('wallet/transfers', [WalletController::class, 'transfer']);

        // Invoices / Payments / Print
        Route::get('invoices', [InvoiceController::class, 'index']);
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show']);
        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print']);
        Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'addPayment']);

        // Accounting periods
        Route::get('accounting/periods', [AccountingPeriodController::class, 'index']);
        Route::get('accounting/periods/{accountingPeriod}', [AccountingPeriodController::class, 'show']);
        Route::post('accounting/periods', [AccountingPeriodController::class, 'store']);
        Route::post('accounting/periods/{accountingPeriod}/close', [AccountingPeriodController::class, 'close']);
        Route::post('accounting/periods/{accountingPeriod}/reopen', [AccountingPeriodController::class, 'reopen']);

        // Reconciliations
        Route::get('reconciliations', [ReconciliationController::class, 'index']);
        Route::get('reconciliations/{reconciliation}', [ReconciliationController::class, 'show']);
        Route::post('reconciliations', [ReconciliationController::class, 'store']);
        Route::post('reconciliations/{reconciliation}/platform-ok', [ReconciliationController::class, 'platformOk']);
        Route::post('reconciliations/{reconciliation}/partner-ok', [ReconciliationController::class, 'partnerOk']);
        Route::post('reconciliations/{reconciliation}/close', [ReconciliationController::class, 'close']);

        // Backward-compatible CRUD endpoints from previous generator (PascalCase resources).
        require __DIR__ . '/api-legacy.php';
    });
});

