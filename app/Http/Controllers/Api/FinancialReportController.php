<?php

namespace App\Http\Controllers\Api;

use App\Models\FinancialReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class FinancialReportController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = FinancialReport::query();

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
            return $this->fail('Failed to fetch financialReport list', $e->getMessage(), 500);
        }
    }

    public function show(FinancialReport $financialReport) {
        try {
            return $this->ok($financialReport);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch financialReport', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new FinancialReport();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = FinancialReport::create($data);
                return $this->ok($created, 'FinancialReport created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create financialReport', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, FinancialReport $financialReport) {
        try {
            $data = $this->filterData($request, $financialReport);

            return DB::transaction(function() use ($data, $financialReport) {
                $financialReport->update($data);
                return $this->ok($financialReport->fresh(), 'FinancialReport updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update financialReport', $e->getMessage(), 422);
        }
    }

    public function destroy(FinancialReport $financialReport) {
        try {
            return DB::transaction(function() use ($financialReport) {
                $financialReport->delete();
                return $this->ok(null, 'FinancialReport deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete financialReport', $e->getMessage(), 500);
        }
    }
}
