<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class EmployeeController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Employee::query();

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
            return $this->fail('Failed to fetch employee list', $e->getMessage(), 500);
        }
    }

    public function show(Employee $employee) {
        try {
            return $this->ok($employee);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch employee', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Employee();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Employee::create($data);
                return $this->ok($created, 'Employee created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create employee', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Employee $employee) {
        try {
            $data = $this->filterData($request, $employee);

            return DB::transaction(function() use ($data, $employee) {
                $employee->update($data);
                return $this->ok($employee->fresh(), 'Employee updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update employee', $e->getMessage(), 422);
        }
    }

    public function destroy(Employee $employee) {
        try {
            return DB::transaction(function() use ($employee) {
                $employee->delete();
                return $this->ok(null, 'Employee deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete employee', $e->getMessage(), 500);
        }
    }
}
