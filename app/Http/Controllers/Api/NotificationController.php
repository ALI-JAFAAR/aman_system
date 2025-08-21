<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class NotificationController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Notification::query();

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
            return $this->fail('Failed to fetch notification list', $e->getMessage(), 500);
        }
    }

    public function show(Notification $notification) {
        try {
            return $this->ok($notification);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch notification', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Notification();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Notification::create($data);
                return $this->ok($created, 'Notification created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create notification', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Notification $notification) {
        try {
            $data = $this->filterData($request, $notification);

            return DB::transaction(function() use ($data, $notification) {
                $notification->update($data);
                return $this->ok($notification->fresh(), 'Notification updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update notification', $e->getMessage(), 422);
        }
    }

    public function destroy(Notification $notification) {
        try {
            return DB::transaction(function() use ($notification) {
                $notification->delete();
                return $this->ok(null, 'Notification deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete notification', $e->getMessage(), 500);
        }
    }
}
