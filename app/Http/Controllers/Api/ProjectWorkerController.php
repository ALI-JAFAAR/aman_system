<?php

namespace App\Http\Controllers\Api;

use App\Models\ProjectWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProjectWorkerController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = ProjectWorker::query();

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
            return $this->fail('Failed to fetch projectWorker list', $e->getMessage(), 500);
        }
    }

    public function show(ProjectWorker $projectWorker) {
        try {
            return $this->ok($projectWorker);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch projectWorker', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new ProjectWorker();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = ProjectWorker::create($data);
                return $this->ok($created, 'ProjectWorker created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create projectWorker', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, ProjectWorker $projectWorker) {
        try {
            $data = $this->filterData($request, $projectWorker);

            return DB::transaction(function() use ($data, $projectWorker) {
                $projectWorker->update($data);
                return $this->ok($projectWorker->fresh(), 'ProjectWorker updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update projectWorker', $e->getMessage(), 422);
        }
    }

    public function destroy(ProjectWorker $projectWorker) {
        try {
            return DB::transaction(function() use ($projectWorker) {
                $projectWorker->delete();
                return $this->ok(null, 'ProjectWorker deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete projectWorker', $e->getMessage(), 500);
        }
    }
}
