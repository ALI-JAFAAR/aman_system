<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactionController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Transaction::query();

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
            return $this->fail('Failed to fetch transaction list', $e->getMessage(), 500);
        }
    }

    public function show(Transaction $transaction) {
        try {
            return $this->ok($transaction);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch transaction', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Transaction();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Transaction::create($data);
                return $this->ok($created, 'Transaction created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create transaction', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Transaction $transaction) {
        try {
            $data = $this->filterData($request, $transaction);

            return DB::transaction(function() use ($data, $transaction) {
                $transaction->update($data);
                return $this->ok($transaction->fresh(), 'Transaction updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update transaction', $e->getMessage(), 422);
        }
    }

    public function destroy(Transaction $transaction) {
        try {
            return DB::transaction(function() use ($transaction) {
                $transaction->delete();
                return $this->ok(null, 'Transaction deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete transaction', $e->getMessage(), 500);
        }
    }
}
