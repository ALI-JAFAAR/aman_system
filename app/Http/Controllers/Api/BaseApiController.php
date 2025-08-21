<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

abstract class BaseApiController extends BaseController {
    protected function ok($data = null, string $message = null, int $code = 200) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function fail($message = 'Something went wrong', $errors = null, int $code = 500) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Filter request data by model fillable if provided, otherwise pass through all input.
     */
    protected function filterData(Request $request, $modelInstance): array {
        $data = $request->all();
        try {
            $fillable = method_exists($modelInstance, 'getFillable') ? $modelInstance->getFillable() : [];
            if (!empty($fillable)) {
                return Arr::only($data, $fillable);
            }
            return $data;
        } catch (Throwable $e) {
            return $data;
        }
    }
}
