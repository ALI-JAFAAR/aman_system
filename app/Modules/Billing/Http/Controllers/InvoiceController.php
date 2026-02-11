<?php

namespace App\Modules\Billing\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class InvoiceController extends BaseApiController
{
    public function index(Request $request)
    {
        try {
            /** @var User $user */
            $user = $request->user();

            $perPage = (int) $request->integer('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Invoice::query()
                ->with(['user', 'issuer'])
                ->latest('id');

            // Access control (basic): non-admin can only see their own invoices
            $isAdmin = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['platform_admin', 'aman_staff']);
            if (! $isAdmin) {
                $query->where('user_id', $user->id);
            } elseif ($filterUserId = $request->get('user_id')) {
                $query->where('user_id', (int) $filterUserId);
            }

            if ($status = $request->get('status')) {
                $query->where('status', (string) $status);
            }

            return $this->ok($query->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch invoices', $e->getMessage(), 500);
        }
    }

    public function show(Request $request, Invoice $invoice)
    {
        /** @var User $user */
        $user = $request->user();
        $isAdmin = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['platform_admin', 'aman_staff']);
        if (! $isAdmin && (int) $invoice->user_id !== (int) $user->id) {
            return $this->fail('Forbidden', null, 403);
        }

        $invoice->loadMissing([
            'user.userProfiles',
            'issuer.user',
            'items',
            'payments',
        ]);

        return $this->ok($invoice);
    }

    public function print(Request $request, Invoice $invoice)
    {
        /** @var User $user */
        $user = $request->user();
        $isAdmin = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['platform_admin', 'aman_staff']);
        if (! $isAdmin && (int) $invoice->user_id !== (int) $user->id) {
            return response('Forbidden', 403);
        }

        $userModel = User::find($invoice->user_id);
        $profile = UserProfile::where('user_id', $invoice->user_id)->latest('id')->first();
        $items = InvoiceItem::where('invoice_id', $invoice->id)->get();
        $payments = Payment::where('invoice_id', $invoice->id)->get();

        return response()->view('invoices.print', [
            'invoice' => $invoice,
            'user' => $userModel,
            'profile' => $profile,
            'items' => $items,
            'payments' => $payments,
        ]);
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'method' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference' => ['nullable', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $isAdmin = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['platform_admin', 'aman_staff']);
        if (! $isAdmin) {
            return $this->fail('Forbidden', null, 403);
        }

        try {
            return DB::transaction(function () use ($invoice, $data, $user) {
                $payment = Payment::create([
                    'invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                    'method' => $data['method'],
                    'amount' => (float) $data['amount'],
                    'reference' => $data['reference'] ?? null,
                    'meta' => $data['meta'] ?? [],
                ]);

                $paid = (float) Payment::where('invoice_id', $invoice->id)->sum('amount');
                $balance = max(0, (float) $invoice->total - $paid);

                $status = $balance <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');

                $invoice->update([
                    'paid' => $paid,
                    'balance' => $balance,
                    'status' => $status,
                ]);

                return $this->ok([
                    'invoice' => $invoice->fresh(['payments']),
                    'payment' => $payment,
                ], 'Payment recorded', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to add payment', $e->getMessage(), 422);
        }
    }
}

