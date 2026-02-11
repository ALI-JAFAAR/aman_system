<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Admin\Http\Controllers\AdministrativeRecordController;
use App\Modules\Admin\Http\Controllers\AuditLogController;
use App\Modules\Admin\Http\Controllers\EmployeeController;
use App\Modules\Catalog\Http\Controllers\ProfessionController;
use App\Modules\Catalog\Http\Controllers\ServiceController;
use App\Modules\Catalog\Http\Controllers\SpecializationController;
use App\Modules\Claims\Http\Controllers\ClaimResponseCrudController;
use App\Modules\Finance\Http\Controllers\BankAccountController;
use App\Modules\Finance\Http\Controllers\FinancialReportController;
use App\Modules\Finance\Http\Controllers\LedgerEntryController;
use App\Modules\Finance\Http\Controllers\ReconciliationEntryController;
use App\Modules\Finance\Http\Controllers\TransactionController;
use App\Modules\Finance\Http\Controllers\WithdrawRequestController;
use App\Modules\Health\Http\Controllers\HealthAnswerController;
use App\Modules\Health\Http\Controllers\VehicleController;
use App\Modules\Insurance\Http\Controllers\OfferingDistributionController;
use App\Modules\Notifications\Http\Controllers\NotificationController;
use App\Modules\Organizations\Http\Controllers\OrganizationSpecializationController;
use App\Modules\Projects\Http\Controllers\ProjectController;
use App\Modules\Projects\Http\Controllers\ProjectWorkerController;
use App\Modules\Users\Http\Controllers\UserAffiliationController;
use App\Modules\Users\Http\Controllers\UserOfferingController;
use App\Modules\Users\Http\Controllers\UserProfessionController;
use App\Modules\Users\Http\Controllers\UserProfileController;
use App\Modules\Users\Http\Controllers\UserServiceController;
use App\Modules\Wallet\Http\Controllers\WalletCrudController;
use App\Modules\Records\Http\Controllers\ProfessionalRecordController;

// Legacy Vemto-style API resources (PascalCase paths). Kept for backward compatibility.
Route::apiResource('AdministrativeRecord', AdministrativeRecordController::class);
Route::apiResource('AuditLog', AuditLogController::class);
Route::apiResource('BankAccount', BankAccountController::class);
Route::apiResource('ClaimResponse', ClaimResponseCrudController::class);
Route::apiResource('Employee', EmployeeController::class);
Route::apiResource('FinancialReport', FinancialReportController::class);
Route::apiResource('HealthAnswer', HealthAnswerController::class);
Route::apiResource('LedgerEntry', LedgerEntryController::class);
Route::apiResource('Notification', NotificationController::class);
Route::apiResource('OfferingDistribution', OfferingDistributionController::class);
Route::apiResource('OrganizationSpecialization', OrganizationSpecializationController::class);
Route::apiResource('Profession', ProfessionController::class);
Route::apiResource('ProfessionalRecord', ProfessionalRecordController::class);
Route::apiResource('Project', ProjectController::class);
Route::apiResource('ProjectWorker', ProjectWorkerController::class);
Route::apiResource('ReconciliationEntry', ReconciliationEntryController::class);
Route::apiResource('Service', ServiceController::class);
Route::apiResource('Specialization', SpecializationController::class);
Route::apiResource('Transaction', TransactionController::class);
Route::apiResource('UserAffiliation', UserAffiliationController::class);
Route::apiResource('UserOffering', UserOfferingController::class);
Route::apiResource('UserProfession', UserProfessionController::class);
Route::apiResource('UserProfile', UserProfileController::class);
Route::apiResource('UserService', UserServiceController::class);
Route::apiResource('Vehicle', VehicleController::class);
Route::apiResource('Wallet', WalletCrudController::class);
Route::apiResource('WithdrawRequest', WithdrawRequestController::class);

