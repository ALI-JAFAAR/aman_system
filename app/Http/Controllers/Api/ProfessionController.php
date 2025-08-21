<?php

namespace App\Http\Controllers\Api;

use App\Models\Profession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProfessionController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Profession::query();

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
            return $this->fail('Failed to fetch profession list', $e->getMessage(), 500);
        }
    }

    public function show(Profession $profession) {
        try {
            return $this->ok($profession);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch profession', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Profession();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Profession::create($data);
                return $this->ok($created, 'Profession created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create profession', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Profession $profession) {
        try {
            $data = $this->filterData($request, $profession);

            return DB::transaction(function() use ($data, $profession) {
                $profession->update($data);
                return $this->ok($profession->fresh(), 'Profession updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update profession', $e->getMessage(), 422);
        }
    }

    public function destroy(Profession $profession) {
        try {
            return DB::transaction(function() use ($profession) {
                $profession->delete();
                return $this->ok(null, 'Profession deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete profession', $e->getMessage(), 500);
        }
    }
}
