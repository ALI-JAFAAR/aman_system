<?php

namespace App\Modules\Wallet\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Modules\Wallet\Http\Requests\TransferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class WalletController extends BaseApiController
{
    public function show(Request $request)
    {
        try {
            /** @var User $user */
            $user = $request->user();

            $wallet = Wallet::query()
                ->where('walletable_type', User::class)
                ->where('walletable_id', $user->id)
                ->first();

            if (! $wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                    'currency' => 'IQD',
                    'walletable_type' => User::class,
                    'walletable_id' => $user->id,
                ]);
            }

            $wallet->loadMissing([
                'transactions' => fn ($q) => $q->latest('id')->limit(50),
            ]);

            return $this->ok($wallet);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch wallet', $e->getMessage(), 500);
        }
    }

    public function transfer(TransferRequest $request)
    {
        try {
            /** @var User $user */
            $user = $request->user();

            $amount = (float) $request->validated('amount');
            $toWalletId = (int) $request->validated('to_wallet_id');
            $description = $request->validated('description');

            return DB::transaction(function () use ($user, $amount, $toWalletId, $description) {
                /** @var Wallet $from */
                $from = Wallet::query()
                    ->where('walletable_type', User::class)
                    ->where('walletable_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                /** @var Wallet $to */
                $to = Wallet::query()
                    ->whereKey($toWalletId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($from->id === $to->id) {
                    return $this->fail('Invalid transfer', ['to_wallet_id' => 'لا يمكن التحويل لنفس المحفظة'], 422);
                }

                if ((float) $from->balance < $amount) {
                    return $this->fail('Insufficient balance', ['amount' => 'الرصيد غير كافٍ'], 422);
                }

                if ($from->currency !== $to->currency) {
                    return $this->fail('Currency mismatch', ['currency' => 'اختلاف العملة غير مدعوم حالياً'], 422);
                }

                $from->balance = (float) $from->balance - $amount;
                $to->balance = (float) $to->balance + $amount;
                $from->save();
                $to->save();

                $tx = Transaction::create([
                    'wallet_id' => $from->id,
                    'transaction_type' => 'transfer',
                    'amount' => $amount,
                    'target_wallet_id' => $to->id,
                    'status' => 'completed',
                    'reference_type' => Wallet::class,
                    'reference_id' => $to->id,
                    'description' => $description,
                ]);

                return $this->ok([
                    'transaction' => $tx,
                    'from_wallet' => $from->fresh(),
                    'to_wallet' => $to->fresh(),
                ], 'تم التحويل بنجاح', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to transfer', $e->getMessage(), 422);
        }
    }
}

