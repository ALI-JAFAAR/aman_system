<?php

namespace App\Http\Controllers\Api;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class WalletController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Wallet::query();

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
            return $this->fail('Failed to fetch wallet list', $e->getMessage(), 500);
        }
    }

    public function show(Wallet $wallet) {
        try {
            return $this->ok($wallet);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch wallet', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Wallet();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Wallet::create($data);
                return $this->ok($created, 'Wallet created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create wallet', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Wallet $wallet) {
        try {
            $data = $this->filterData($request, $wallet);

            return DB::transaction(function() use ($data, $wallet) {
                $wallet->update($data);
                return $this->ok($wallet->fresh(), 'Wallet updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update wallet', $e->getMessage(), 422);
        }
    }

    public function destroy(Wallet $wallet) {
        try {
            return DB::transaction(function() use ($wallet) {
                $wallet->delete();
                return $this->ok(null, 'Wallet deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete wallet', $e->getMessage(), 500);
        }
    }
}
