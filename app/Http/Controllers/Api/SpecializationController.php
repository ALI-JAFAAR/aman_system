<?php

namespace App\Http\Controllers\Api;

use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SpecializationController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Specialization::query();

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
            return $this->fail('Failed to fetch specialization list', $e->getMessage(), 500);
        }
    }

    public function show(Specialization $specialization) {
        try {
            return $this->ok($specialization);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch specialization', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Specialization();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Specialization::create($data);
                return $this->ok($created, 'Specialization created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create specialization', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Specialization $specialization) {
        try {
            $data = $this->filterData($request, $specialization);

            return DB::transaction(function() use ($data, $specialization) {
                $specialization->update($data);
                return $this->ok($specialization->fresh(), 'Specialization updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update specialization', $e->getMessage(), 422);
        }
    }

    public function destroy(Specialization $specialization) {
        try {
            return DB::transaction(function() use ($specialization) {
                $specialization->delete();
                return $this->ok(null, 'Specialization deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete specialization', $e->getMessage(), 500);
        }
    }
}
