<?php

namespace App\Http\Controllers\Api;

use App\Models\ProfessionalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProfessionalRecordController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = ProfessionalRecord::query();

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
            return $this->fail('Failed to fetch professionalRecord list', $e->getMessage(), 500);
        }
    }

    public function show(ProfessionalRecord $professionalRecord) {
        try {
            return $this->ok($professionalRecord);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch professionalRecord', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new ProfessionalRecord();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = ProfessionalRecord::create($data);
                return $this->ok($created, 'ProfessionalRecord created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create professionalRecord', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, ProfessionalRecord $professionalRecord) {
        try {
            $data = $this->filterData($request, $professionalRecord);

            return DB::transaction(function() use ($data, $professionalRecord) {
                $professionalRecord->update($data);
                return $this->ok($professionalRecord->fresh(), 'ProfessionalRecord updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update professionalRecord', $e->getMessage(), 422);
        }
    }

    public function destroy(ProfessionalRecord $professionalRecord) {
        try {
            return DB::transaction(function() use ($professionalRecord) {
                $professionalRecord->delete();
                return $this->ok(null, 'ProfessionalRecord deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete professionalRecord', $e->getMessage(), 500);
        }
    }
}
