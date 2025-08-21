<?php

namespace App\Http\Controllers\Api;

use App\Models\AdministrativeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class AdministrativeRecordController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = AdministrativeRecord::query();

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
            return $this->fail('Failed to fetch administrativeRecord list', $e->getMessage(), 500);
        }
    }

    public function show(AdministrativeRecord $administrativeRecord) {
        try {
            return $this->ok($administrativeRecord);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch administrativeRecord', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new AdministrativeRecord();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = AdministrativeRecord::create($data);
                return $this->ok($created, 'AdministrativeRecord created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create administrativeRecord', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, AdministrativeRecord $administrativeRecord) {
        try {
            $data = $this->filterData($request, $administrativeRecord);

            return DB::transaction(function() use ($data, $administrativeRecord) {
                $administrativeRecord->update($data);
                return $this->ok($administrativeRecord->fresh(), 'AdministrativeRecord updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update administrativeRecord', $e->getMessage(), 422);
        }
    }

    public function destroy(AdministrativeRecord $administrativeRecord) {
        try {
            return DB::transaction(function() use ($administrativeRecord) {
                $administrativeRecord->delete();
                return $this->ok(null, 'AdministrativeRecord deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete administrativeRecord', $e->getMessage(), 500);
        }
    }
}
