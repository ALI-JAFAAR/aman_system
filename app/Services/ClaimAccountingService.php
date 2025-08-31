<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\LedgerEntry;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class ClaimAccountingService
{
    /**
     * عند اعتماد المطالبة تُثبت قيد مصروف + قيد التزام (مستحقات مطالبات)
     * 5100: مصروف مطالبات    (مدين)
     * 2300: مستحقات مطالبات   (دائن)
     */
    public function approve(Claim $claim, float $approvedAmount, ?int $employeeId = null): Claim
    {
        return DB::transaction(function () use ($claim, $approvedAmount, $employeeId) {

            // حدّث حالة المطالبة
            $claim->update([
                'status'          => 'approved',
                'amount_approved' => $approvedAmount,
                'approved_at'     => now(),
                'approved_by'     => $employeeId,
            ]);

            $orgId = $claim->organization_id ?: null; // غيّرها لو عندك منطق لتحديد الجهة

            // 5100 مصروف المطالبات (مدين)
            LedgerEntry::create([
                'entry_date'      => now(),
                'organization_id' => $orgId,
                'account_code'    => '5100',
                'debit'           => $approvedAmount,
                'credit'          => 0,
                'currency'        => 'IQD',
                'description'     => 'مصروف مطالبة #' . $claim->id,
            ]);

            // 2300 مستحقات المطالبات (دائن)
            LedgerEntry::create([
                'entry_date'      => now(),
                'organization_id' => $orgId,
                'account_code'    => '2300',
                'debit'           => 0,
                'credit'          => $approvedAmount,
                'currency'        => 'IQD',
                'description'     => 'مستحقات مطالبة #' . $claim->id,
            ]);

            return $claim->fresh();
        });
    }

    /**
     * عند الصرف: نقفل الالتزام ونخصم من الصندوق/البنك أو نضيف لمحفظة المستخدم
     * 2300: مستحقات مطالبات (مدين)
     * 1000/1010: نقد/بنك   (دائن)  — أو إلى محفظة المنتسب
     *
     * $payToWallet إذا true تُضاف لمحفظة المنتسب بدل قيد النقد/البنك.
     */
    public function pay(Claim $claim, string $method, ?int $employeeId = null, bool $payToWallet = false): Claim
    {
        return DB::transaction(function () use ($claim, $method, $employeeId, $payToWallet) {

            $amount = (float) $claim->amount_approved;
            if ($amount <= 0) {
                return $claim;
            }

            $orgId = $claim->organization_id ?: null;

            // اقفل الالتزام 2300 (مدين)
            LedgerEntry::create([
                'entry_date'      => now(),
                'organization_id' => $orgId,
                'account_code'    => '2300',
                'debit'           => $amount,
                'credit'          => 0,
                'currency'        => 'IQD',
                'description'     => 'تسوية مستحقات مطالبة #' . $claim->id,
            ]);

            if ($payToWallet) {
                // أضف للمحفظة
                $wallet = Wallet::firstOrCreate([
                    'walletable_type' => \App\Models\User::class,
                    'walletable_id'   => $claim->user_id,
                ], [
                    'user_id'  => $claim->user_id,
                    'balance'  => 0,
                    'currency' => 'IQD',
                ]);

                $wallet->increment('balance', $amount);

                // قيد مقابل للمحفظة (نعتبرها التزام آخر 2400 مثلًا)
                LedgerEntry::create([
                    'entry_date'      => now(),
                    'organization_id' => $orgId,
                    'account_code'    => '2400', // التزامات محافظ
                    'debit'           => 0,
                    'credit'          => $amount,
                    'currency'        => 'IQD',
                    'description'     => 'إيداع إلى محفظة المنتسب لمطالبة #' . $claim->id,
                ]);

            } else {
                // 1000/1010 حسب طريقة الدفع
                $cashCode = in_array($method, ['cash','pos']) ? '1000' : '1010';

                LedgerEntry::create([
                    'entry_date'      => now(),
                    'organization_id' => $orgId,
                    'account_code'    => $cashCode,
                    'debit'           => 0,
                    'credit'          => $amount,
                    'currency'        => 'IQD',
                    'description'     => 'صرف مطالبة #' . $claim->id . " ({$method})",
                ]);
            }

            $claim->update([
                'status'        => 'paid',
                'paid_at'       => now(),
                'paid_by'       => $employeeId,
                'payment_method'=> $method,
            ]);

            return $claim->fresh();
        });
    }

    /**
     * رفض المطالبة: مجرّد تغيير حالة مع تسجيل سبب (لا قيود).
     */
    public function reject(Claim $claim, ?string $reason, ?int $employeeId = null): Claim
    {
        $claim->update([
            'status'     => 'rejected',
            'rejected_at'=> now(),
            'rejected_by'=> $employeeId,
            'admin_note' => $reason,
        ]);

        return $claim->fresh();
    }
}
