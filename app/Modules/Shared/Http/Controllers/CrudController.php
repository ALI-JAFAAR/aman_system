<?php

namespace App\Modules\Shared\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Generic CRUD controller for simple models.
 * This is mainly to restore old generated endpoints, but kept inside Modules.
 */
abstract class CrudController extends BaseApiController
{
    /** @var class-string<\Illuminate\Database\Eloquent\Model> */
    protected string $modelClass;

    protected function query(Request $request)
    {
        $modelClass = $this->modelClass;
        $q = $modelClass::query()->latest('id');

        if ($search = trim((string) $request->get('q', ''))) {
            // Default lightweight search: id exact/like
            $q->where('id', 'like', "%{$search}%");
        }

        return $q;
    }

    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            return $this->ok($this->query($request)->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch list', $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $modelClass = $this->modelClass;
        return $this->ok($modelClass::query()->findOrFail($id));
    }

    public function store(Request $request)
    {
        try {
            $modelClass = $this->modelClass;
            $instance = new $modelClass();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function () use ($modelClass, $data) {
                $created = $modelClass::query()->create($data);
                return $this->ok($created, 'Created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $modelClass = $this->modelClass;
            $model = $modelClass::query()->findOrFail($id);
            $data = $this->filterData($request, $model);

            return DB::transaction(function () use ($model, $data) {
                $model->update($data);
                return $this->ok($model->fresh(), 'Updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update', $e->getMessage(), 422);
        }
    }

    public function destroy($id)
    {
        try {
            $modelClass = $this->modelClass;
            $model = $modelClass::query()->findOrFail($id);

            return DB::transaction(function () use ($model) {
                $model->delete();
                return $this->ok(null, 'Deleted');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete', $e->getMessage(), 500);
        }
    }
}

