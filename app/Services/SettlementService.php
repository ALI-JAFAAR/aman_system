<?php

namespace App\Services;

use App\Models\LedgerEntry;
use App\Models\UserOffering;
use Illuminate\Support\Facades\DB;

class SettlementService
{
    // نفس أكوادك
    public const ACC_PAY_PARTNER = AffiliationPostingService::ACC_PAY_PARTNER; // 2100
    public const ACC_PAY_HOST    = AffiliationPostingService::ACC_PAY_HOST;    // 2200
    public const ACC_CASH        = AffiliationPostingService::ACC_CASH;        // 1000
    public const ACC_POS         = AffiliationPostingService::ACC_POS;         // 1010
    public const ACC_ZAINCASH    = AffiliationPostingService::ACC_ZAINCASH;    // 1011
    public const ACC_BANK        = AffiliationPostingService::ACC_BANK;        // 1020

    /** تحقق سريع ضد فترات مُقفلة */
    protected function assertNotClosed(\DateTimeInterface $postedAt): void{
        if (class_exists(\App\Models\AccountingPeriod::class)) {
            $exists = \App\Models\AccountingPeriod::query()
                ->where('is_closed', true)
                ->where('start_date', '<=', $postedAt)
                ->where('end_date',   '>=', $postedAt)
                ->exists();
            if ($exists) {
                throw new \RuntimeException('الفترة المحاسبية مُقفلة ولا يمكن إضافة قيود داخلها.');
            }
        }
    }

    protected function cashAccount(string $method): string{
        return match ($method) {
            'pos'      => self::ACC_POS,
            'zaincash' => self::ACC_ZAINCASH,
            'bank'     => self::ACC_BANK,
            default    => self::ACC_CASH,
        };
    }

    /** تسوية مع الشريك (شركة تأمين): Dr Cash/Bank Cr 2100 */
    public function settlePartner(int $partnerOrgId, float $amount, string $method, \DateTimeInterface $postedAt, ?int $employeeId = null, ?string $note = null): void{
        $this->assertNotClosed($postedAt);
        $amount = round(max(0, $amount), 2);
        if ($amount <= 0) return;

        DB::transaction(function () use ($partnerOrgId, $amount, $method, $postedAt, $employeeId, $note) {
            $desc = $note ?: 'تسوية مستحقات الشريك';
            $cashAcc = $this->cashAccount($method);

            // مدين: الصندوق/البنك
            LedgerEntry::create([
                'invoice_id'     => null,
                'reference_type' => null,
                'reference_id'   => null,
                'account_code'   => $cashAcc,
                'entry_type'     => 'debit',
                'amount'         => $amount,
                'description'    => $desc,
                'created_by'     => $employeeId,
                'is_locked'      => false,
                'posted_at'      => $postedAt,
            ]);

            // دائن: 2100 BUT مقيّد الى الشريك عبر user_offerings؟
            // هنا لا نربطه بـ reference معين؛ نُسجّل قيد عام على 2100.
            LedgerEntry::create([
                'invoice_id'     => null,
                'reference_type' => null,
                'reference_id'   => null,
                'account_code'   => self::ACC_PAY_PARTNER,
                'entry_type'     => 'credit',
                'amount'         => $amount,
                'description'    => $desc . " — Org#{$partnerOrgId}",
                'created_by'     => $employeeId,
                'is_locked'      => false,
                'posted_at'      => $postedAt,
                // للمطابقة التفصيلية يمكنك لاحقًا توزيعها على سطور 2100 المرتبطة بـ uo/po نفسها.
            ]);
        });
    }

    /** تسوية مع الجهة المضيفة: Dr Cash/Bank Cr 2200 */
    public function settleHost(int $hostOrgId, float $amount, string $method, \DateTimeInterface $postedAt, ?int $employeeId = null, ?string $note = null): void
    {
        $this->assertNotClosed($postedAt);
        $amount = round(max(0, $amount), 2);
        if ($amount <= 0) return;

        DB::transaction(function () use ($hostOrgId, $amount, $method, $postedAt, $employeeId, $note) {
            $desc = $note ?: 'تسوية مستحقات الجهة';
            $cashAcc = $this->cashAccount($method);

            LedgerEntry::create([
                'invoice_id'     => null,
                'reference_type' => null,
                'reference_id'   => null,
                'account_code'   => $cashAcc,
                'entry_type'     => 'debit',
                'amount'         => $amount,
                'description'    => $desc,
                'created_by'     => $employeeId,
                'is_locked'      => false,
                'posted_at'      => $postedAt,
            ]);

            LedgerEntry::create([
                'invoice_id'     => null,
                'reference_type' => null,
                'reference_id'   => null,
                'account_code'   => self::ACC_PAY_HOST,
                'entry_type'     => 'credit',
                'amount'         => $amount,
                'description'    => $desc . " — Org#{$hostOrgId}",
                'created_by'     => $employeeId,
                'is_locked'      => false,
                'posted_at'      => $postedAt,
            ]);
        });
    }
}
