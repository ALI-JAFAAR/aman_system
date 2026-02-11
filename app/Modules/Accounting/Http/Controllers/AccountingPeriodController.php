<?php

namespace App\Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\AccountingPeriod;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class AccountingPeriodController extends BaseApiController
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            return $this->ok(
                AccountingPeriod::query()->latest('id')->paginate($perPage)
            );
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch accounting periods', $e->getMessage(), 500);
        }
    }

    public function show(AccountingPeriod $accountingPeriod)
    {
        return $this->ok($accountingPeriod);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        try {
            $period = AccountingPeriod::create($data + ['is_closed' => false]);
            return $this->ok($period, 'Accounting period created', 201);
        } catch (Throwable $e) {
            return $this->fail('Failed to create accounting period', $e->getMessage(), 422);
        }
    }

    public function close(AccountingPeriod $accountingPeriod)
    {
        if ($accountingPeriod->is_closed) {
            return $this->ok($accountingPeriod, 'Already closed');
        }

        try {
            return DB::transaction(function () use ($accountingPeriod) {
                LedgerEntry::whereDate('posted_at','>=',$accountingPeriod->start_date)
                    ->whereDate('posted_at','<=',$accountingPeriod->end_date)
                    ->update(['is_locked' => true]);

                $accountingPeriod->is_closed = true;
                $accountingPeriod->closed_by = optional(auth()->user()?->employee)->id;
                $accountingPeriod->closed_at = now();
                $accountingPeriod->save();

                return $this->ok($accountingPeriod->fresh(), 'Period closed');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to close period', $e->getMessage(), 422);
        }
    }

    public function reopen(AccountingPeriod $accountingPeriod)
    {
        if (! $accountingPeriod->is_closed) {
            return $this->ok($accountingPeriod, 'Already open');
        }

        try {
            return DB::transaction(function () use ($accountingPeriod) {
                LedgerEntry::whereDate('posted_at','>=',$accountingPeriod->start_date)
                    ->whereDate('posted_at','<=',$accountingPeriod->end_date)
                    ->update(['is_locked' => false]);

                $accountingPeriod->is_closed = false;
                $accountingPeriod->closed_by = null;
                $accountingPeriod->closed_at = null;
                $accountingPeriod->save();

                return $this->ok($accountingPeriod->fresh(), 'Period reopened');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to reopen period', $e->getMessage(), 422);
        }
    }
}

