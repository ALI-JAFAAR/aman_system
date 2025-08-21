<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProjectController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Project::query();

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
            return $this->fail('Failed to fetch project list', $e->getMessage(), 500);
        }
    }

    public function show(Project $project) {
        try {
            return $this->ok($project);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch project', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Project();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Project::create($data);
                return $this->ok($created, 'Project created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create project', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Project $project) {
        try {
            $data = $this->filterData($request, $project);

            return DB::transaction(function() use ($data, $project) {
                $project->update($data);
                return $this->ok($project->fresh(), 'Project updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update project', $e->getMessage(), 422);
        }
    }

    public function destroy(Project $project) {
        try {
            return DB::transaction(function() use ($project) {
                $project->delete();
                return $this->ok(null, 'Project deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete project', $e->getMessage(), 500);
        }
    }
}
