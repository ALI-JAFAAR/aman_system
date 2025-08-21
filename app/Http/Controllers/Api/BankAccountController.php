<?php

namespace App\Http\Controllers\Api;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class BankAccountController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = BankAccount::query();

            // Optionally add simple filters: ?q=search
            if ($search = $request->get('q')) {
                // naive search over 'id' and timestamp columns; customize later
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%");
                });
            }

            $data = $query->paginate($perPage);
            return $this->ok($data);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch bankAccount list', $e->getMessage(), 500);
        }
    }

    public function show(BankAccount $bankAccount) {
        try {
            return $this->ok($bankAccount);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch bankAccount', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new BankAccount();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = BankAccount::create($data);
                return $this->ok($created, 'BankAccount created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create bankAccount', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, BankAccount $bankAccount) {
        try {
            $data = $this->filterData($request, $bankAccount);

            return DB::transaction(function() use ($data, $bankAccount) {
                $bankAccount->update($data);
                return $this->ok($bankAccount->fresh(), 'BankAccount updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update bankAccount', $e->getMessage(), 422);
        }
    }

    public function destroy(BankAccount $bankAccount) {
        try {
            return DB::transaction(function() use ($bankAccount) {
                $bankAccount->delete();
                return $this->ok(null, 'BankAccount deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete bankAccount', $e->getMessage(), 500);
        }
    }
}
