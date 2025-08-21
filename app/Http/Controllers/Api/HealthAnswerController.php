<?php

namespace App\Http\Controllers\Api;

use App\Models\HealthAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthAnswerController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = HealthAnswer::query();

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
            return $this->fail('Failed to fetch healthAnswer list', $e->getMessage(), 500);
        }
    }

    public function show(HealthAnswer $healthAnswer) {
        try {
            return $this->ok($healthAnswer);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch healthAnswer', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new HealthAnswer();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = HealthAnswer::create($data);
                return $this->ok($created, 'HealthAnswer created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create healthAnswer', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, HealthAnswer $healthAnswer) {
        try {
            $data = $this->filterData($request, $healthAnswer);

            return DB::transaction(function() use ($data, $healthAnswer) {
                $healthAnswer->update($data);
                return $this->ok($healthAnswer->fresh(), 'HealthAnswer updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update healthAnswer', $e->getMessage(), 422);
        }
    }

    public function destroy(HealthAnswer $healthAnswer) {
        try {
            return DB::transaction(function() use ($healthAnswer) {
                $healthAnswer->delete();
                return $this->ok(null, 'HealthAnswer deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete healthAnswer', $e->getMessage(), 500);
        }
    }
}
