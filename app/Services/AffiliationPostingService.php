<?php

namespace App\Services;

use App\Models\{User,
    Employee,
    Organization,
    UserAffiliation,
    UserOffering,
    PartnerOffering,
    OfferingDistribution,
    LedgerEntry,
    Invoice,
    InvoiceItem,
    Payment,
    UserProfession};
use Illuminate\Support\Facades\DB;

class AffiliationPostingService
{
    /** حسابات مخطط الحسابات */
    public const ACC_AR          = '1100'; // ذمم مدينة
    public const ACC_REV_AFF     = '4100'; // إيراد رسوم انتساب
    public const ACC_REV_PKG     = '4201'; // إيراد المنصّة من الباقات
    public const ACC_REV_SVC     = '4300'; // إيراد خدمات إضافية
    public const ACC_PAY_PARTNER = '2100'; // مستحقات الشريك
    public const ACC_PAY_HOST    = '2200'; // مستحقات الجهة
    public const ACC_DISC        = '4910'; // خصومات مبيعات
    public const ACC_CASH        = '1000'; // صندوق
    public const ACC_POS         = '1010'; // نقاط بيع
    public const ACC_ZAINCASH    = '1011'; // زين كاش
    public const ACC_BANK        = '1020'; // بنك

    /**
     * $payload المتوقع:
     * - affiliations[]: [organization_id, affiliation_fee, joined_at, status]
     * - offerings[]: [partner_offering_id, host_organization_id?, notes?]
     * - services[]: [service_id, description, price]
     * - discount_type: none|percent|fixed
     * - discount_value: number
     * - discount_funded_by: platform|partner|host|shared
     * - take_payment_now: bool
     * - payment_method: cash|pos|zaincash|bank
     * - paid_amount: number
     */
    public function post(array $payload, int $issuerEmployeeId, int $userId,$user): Invoice{
        return DB::transaction(function () use ($user, $payload, $issuerEmployeeId, $userId) {

            $issuer      = Employee::with('organization')->findOrFail($issuerEmployeeId);
            $issuerOrgId = $issuer->organization_id;
            // 1) إنشاء الفاتورة
            $invoice = Invoice::create([
                'user_id'            => $userId,
                'issuer_employee_id' => $issuerEmployeeId,
                'number'             => $this->nextInvoiceNumber(),
                'issued_at'          => now(),
                'currency'           => 'IQD',
                'status'             => 'unpaid',
                'discount_type'      => $payload['discount_type']      ?? 'none',
                'discount_value'     => (float)($payload['discount_value'] ?? 0),
                'discount_funded_by' => $payload['discount_funded_by'] ?? 'platform',
            ]);

            $subtotal  = 0.0;
            $createdBy = $issuerEmployeeId;
            // 2) الانتسابات + رقم الهوية + بند + قيود
            foreach (($payload['affiliations'] ?? []) as $row) {
                $aff = UserAffiliation::create([
                    'user_id'         => $userId,
                    'organization_id' => $row['organization_id'],
                    'status'          => $row['status']    ?? 'pending',
                    'joined_at'       => $row['joined_at'] ?? now(),
                ]);
                // رقم هوية الانتساب من المؤسسة
                if ($org = Organization::find($row['organization_id'])) {
                    $identityNumber = $this->nextOrgIdentity($org);
                    $aff->identity_number = $identityNumber;
                    $aff->save();
                }
                UserProfession::create([
                    'user_affiliation_id' => $aff->id,
                    'profession_id'       => $row['profession_id'],
                    'specialization_id'   => $row['specialization_id'] ?? null,
                    'status'              => 'active',
                ]);
                $fee = (float)($row['affiliation_fee'] ?? 0);
                if ($fee > 0) {
                    $subtotal += $fee;
                    InvoiceItem::create([
                        'invoice_id'      => $invoice->id,
                        'item_type'       => 'affiliation_fee',
                        'reference_type'  => UserAffiliation::class,
                        'reference_id'    => $aff->id,
                        'organization_id' => $row['organization_id'],
                        'description'     => 'رسوم انتساب',
                        'qty'             => 1,
                        'unit_price'      => $fee,
                        'line_total'      => $fee,
                        'distribution_snapshot' => null,
                    ]);

                    // ذمم + إيراد رسوم انتساب
                    $this->ledger(self::ACC_AR,      'debit',  $fee, 'رسوم انتساب',          $invoice->id, $createdBy, $aff);
                    $this->ledger(self::ACC_REV_AFF, 'credit', $fee, 'إيراد رسوم انتساب',     $invoice->id, $createdBy, $aff);
                }
            }

            // 3) الباقات (عرض الشريك) + رقم التأمين + توزيع + بند + قيود
            foreach (($payload['offerings'] ?? []) as $row) {
                /** @var PartnerOffering|null $po */
                $po = PartnerOffering::with(['organization','package'])->find($row['partner_offering_id'] ?? null);
                if (!$po) { continue; }

                $price = (float)($po->price ?? 0);
                if ($price <= 0) { continue; }
                $subtotal += $price;

                // إنشاء UserOffering
                $uo = UserOffering::create([
                    'user_id'             => $userId,
                    'partner_offering_id' => $po->id,
                    'status'              => 'applied',
                    'applied_at'          => now(),
                    'notes'               => $row['notes'] ?? null,
                ]);

                // أرقام التأمين
                if ((int)$po->partner_must_fill_number === 1) {
                } else {
                    $uo->platform_generated_number = $this->nextOrgIdentity($po->organization, 'POL');

                    if ((int) $po->auto_approve === 1) {
                        $uo->status       = 'active';
                        $uo->activated_at = now();
                    }
                    $uo->save();
                }

                // الجهة المضيفة
                $hostOrgId = $row['host_organization_id'] ?? UserAffiliation::where('user_id', $userId)->value('organization_id');

                // التوزيع
                [$partnerPct, $platformPct, $hostPctBase] = $this->resolveDistribution($po, $hostOrgId);
                $hostPct = $this->shouldEnableHostShare($issuerOrgId, $hostOrgId) ? $hostPctBase : 0.0;

                $partnerShare  = round($price * $partnerPct  / 100, 2);
                $hostShare     = round($price * $hostPct     / 100, 2);
                $platformShare = round($price * ($platformPct - $hostPct) / 100, 2);
                $diff = $price - ($partnerShare + $hostShare + $platformShare);
                if (abs($diff) >= 0.01) $platformShare += $diff;

                // بند الفاتورة (لقطة توزيع JSON آمنة)
                $snapshot = json_encode([
                    'partner_percent'  => $partnerPct,
                    'platform_percent' => $platformPct,
                    'host_org_percent' => $hostPctBase,
                    'enabled_host_pct' => $hostPct,
                ], JSON_UNESCAPED_UNICODE);

                InvoiceItem::create([
                    'invoice_id'      => $invoice->id,
                    'item_type'       => 'offering',
                    'reference_type'  => UserOffering::class,
                    'reference_id'    => $uo->id,
                    'organization_id' => $hostOrgId,
                    'description'     => 'باقة ' . ($po->package->name ?? ('#' . $po->id)),
                    'qty'             => 1,
                    'unit_price'      => $price,
                    'line_total'      => $price,
                    'partner_share'   => $partnerShare,
                    'host_share'      => $hostShare,
                    'platform_share'  => $platformShare,
                    'distribution_snapshot' => $snapshot,
                ]);

                // ذمم
                $this->ledger(self::ACC_AR, 'debit', $price, 'بيع باقة', $invoice->id, $createdBy, $uo);

                // دائنات التوزيع
                if ($partnerShare  > 0) $this->ledger(self::ACC_PAY_PARTNER, 'credit', $partnerShare,  'مستحق للشريك',           $invoice->id, $createdBy, $uo);
                if ($hostShare     > 0) $this->ledger(self::ACC_PAY_HOST,    'credit', $hostShare,     'مستحق للجهة',            $invoice->id, $createdBy, $uo);
                if ($platformShare > 0) $this->ledger(self::ACC_REV_PKG,     'credit', $platformShare, 'إيراد المنصّة من الباقة', $invoice->id, $createdBy, $uo);
            }

            // 4) خدمات إضافية (كاملها للمنصّة هنا)
            foreach (($payload['services'] ?? []) as $svc) {
                $amount = (float)($svc['price'] ?? 0);
                if ($amount <= 0) continue;

                $desc = $svc['description'] ?? 'خدمة إضافية';
                $subtotal += $amount;

                InvoiceItem::create([
                    'invoice_id'      => $invoice->id,
                    'item_type'       => 'service',
                    'reference_type'  => null,
                    'reference_id'    => null,
                    'organization_id' => null,
                    'description'     => $desc,
                    'qty'             => 1,
                    'unit_price'      => $amount,
                    'line_total'      => $amount,
                    'distribution_snapshot' => null,
                ]);

                // نمرر $reference = null => ستُربط تلقائيًا بالفاتورة داخل ledger()
                $this->ledger(self::ACC_AR,     'debit',  $amount, $desc,$invoice->id, $createdBy);
                $this->ledger(self::ACC_REV_SVC,'credit', $amount, 'إيراد خدمة إضافية', $invoice->id, $createdBy);
            }

            // 5) الخصم على مستوى الفاتورة
            $invoice->subtotal = round($subtotal, 2);

            $discount = $this->calcDiscount(
                (string)$invoice->discount_type,
                (float)$invoice->discount_value,
                (float)$invoice->subtotal
            );

            $invoice->discount_amount = $discount;
            $invoice->total   = round($invoice->subtotal - $discount, 2);
            $invoice->paid    = 0;
            $invoice->balance = $invoice->total;

            if ($discount > 0) {
                switch ($invoice->discount_funded_by) {
                    case 'platform':
                        $this->ledger(self::ACC_AR,   'credit', $discount, 'خصم ممول من المنصّة',    $invoice->id, $createdBy);
                        $this->ledger(self::ACC_DISC, 'debit',  $discount, 'خصم مبيعات (منصّة)',     $invoice->id, $createdBy);
                        break;

                    case 'partner':
                        $this->ledger(self::ACC_AR,          'credit', $discount, 'خصم ممول من الشريك',    $invoice->id, $createdBy);
                        $this->ledger(self::ACC_PAY_PARTNER, 'debit',  $discount, 'تخفيض مستحقات الشريك', $invoice->id, $createdBy);
                        break;

                    case 'host':
                        $this->ledger(self::ACC_AR,       'credit', $discount, 'خصم ممول من الجهة',      $invoice->id, $createdBy);
                        $this->ledger(self::ACC_PAY_HOST, 'debit',  $discount, 'تخفيض مستحقات الجهة',    $invoice->id, $createdBy);
                        break;

                    case 'shared':
                        // تقسيم بسيط 50/50 بين المنصّة والشريك (يمكن تمرير breakdown لاحقًا)
                        $p1 = round($discount / 2, 2);
                        $p2 = $discount - $p1;

                        $this->ledger(self::ACC_AR,   'credit', $discount, 'خصم ممول مشترك',         $invoice->id, $createdBy);
                        $this->ledger(self::ACC_DISC, 'debit',  $p1,       'خصم مبيعات (حصة المنصّة)', $invoice->id, $createdBy);
                        $this->ledger(self::ACC_PAY_PARTNER, 'debit', $p2, 'تخفيض مستحقات الشريك (حصة الشريك)', $invoice->id, $createdBy);
                        break;
                }
            }

            // 6) تحصيل فوري (اختياري)
            if (!empty($payload['take_payment_now'])) {
                $paid = (float)($payload['paid_amount'] ?? $invoice->total);
                $paid = max(0, min($paid, (float)$invoice->total));

                if ($paid > 0) {
                    $cashAcc = match ($payload['payment_method'] ?? 'cash') {
                        'pos'      => self::ACC_POS,
                        'zaincash' => self::ACC_ZAINCASH,
                        'bank'     => self::ACC_BANK,
                        default    => self::ACC_CASH,
                    };

                    // قيود التحصيل
                    $this->ledger($cashAcc,       'debit',  $paid, 'تحصيل فوري',           $invoice->id, $createdBy);
                    $this->ledger(self::ACC_AR,   'credit', $paid, 'تخفيض الذمم بالتحصيل', $invoice->id, $createdBy);

                    // تسجيل الدفعة
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'user_id'    => $userId,
                        'method'     => $payload['payment_method'] ?? 'cash',
                        'amount'     => $paid,
                        'reference'  => $payload['payment_reference'] ?? null,
                        'meta'       => $payload['payment_meta'] ?? null,
                    ]);

                    $invoice->paid    = $paid;
                    $invoice->balance = $invoice->total - $paid;
                    $invoice->status  = $invoice->balance <= 0 ? 'paid' : 'partial';
                }
            }

            $invoice->save();

            return $invoice;
        });
    }

    /**
     * إنشاء قيد محاسبي مع ربط إلزامي بالفاتورة.
     * لو لم يُمرَّر مرجع، نضع مرجع الفاتورة نفسها لتجنّب NULL في reference_type/reference_id.
     */
    protected function ledger(
        string $account,
        string $type,                 // 'debit' | 'credit'
        float $amount,
        string $desc,
        ?int $invoiceId = null,
        ?int $createdByEmployeeId = null,
               $reference = null      // موديل مرجعي اختياري
    ): void {
        $amount = round((float)$amount, 2);
        if ($amount <= 0) return;

        $refType = $reference ? get_class($reference) : Invoice::class;
        $refId   = $reference->id ?? $invoiceId;   // لو لا مرجع، اربط بالـ Invoice

        LedgerEntry::create([
            'invoice_id'     => $invoiceId,
            'reference_type' => $refType,
            'reference_id'   => $refId,
            'account_code'   => $account,
            'entry_type'     => $type,
            'amount'         => $amount,
            'description'    => $desc,
            'created_by'     => $createdByEmployeeId,
            'is_locked'      => false,
            'posted_at'      => now(),
        ]);
    }

    /** حساب قيمة الخصم */
    protected function calcDiscount(string $type, float $value, float $base): float{
        return match ($type) {
            'percent' => round($base * max(0, min($value, 100)) / 100, 2),
            'fixed'   => round(max(0, min($value, $base)), 2),
            default   => 0.0,
        };
    }

    /** إيجاد توزيع العرض (خاص بالجهة إن وُجد، وإلا العام) */
    protected function resolveDistribution(PartnerOffering $po, ?int $hostOrgId): array{
        // توزيع خاص بالجهة
        if ($hostOrgId) {
            $spec = OfferingDistribution::where('partner_offering_id', $po->id)
                ->where('organization_id', $hostOrgId)
                ->where('status', 'active')
                ->first();
            if ($spec) {
                return [
                    (float)$spec->partner_percent,
                    (float)$spec->platform_percent,
                    (float)$spec->host_org_percent,
                ];
            }
        }

        // توزيع عام للعرض
        $d = OfferingDistribution::where('partner_offering_id', $po->id)
            ->whereNull('organization_id')
            ->where('status', 'active')
            ->first();

        if ($d) {
            return [
                (float)$d->partner_percent,
                (float)$d->platform_percent,
                (float)$d->host_org_percent,
            ];
        }

        // افتراضي (كلها للمنصّة)
        return [0.0, 100.0, 0.0];
    }

    /** هل الموظّف تابع لنفس الجهة (أو قريب هرميًا)؟ */
    protected function shouldEnableHostShare(?int $issuerOrgId, ?int $hostOrgId): bool{
        if (!$issuerOrgId || !$hostOrgId) return false;
        if ($issuerOrgId === $hostOrgId) return true;

        $host   = Organization::find($hostOrgId);
        $issuer = Organization::find($issuerOrgId);
        if (!$host || !$issuer) return false;

        // فحص بسيط (أب/ابن). وسّعه حسب هيكل منظمتك.
        return ($host->organization_id === $issuerOrgId) || ($issuer->organization_id === $hostOrgId);
    }

    /** توليد رقم متسلسل داخل الجهة: CODE-000001 (أو PREFIX-000001) */
    public function nextOrgIdentity(Organization $org, ?string $prefix = null, int $pad = 6): string{
        return DB::transaction(function () use ($org, $prefix, $pad) {
            $o   = Organization::lockForUpdate()->find($org->id);
            $seq = (int)($o->next_identity_sequence ?? 0) + 1;
            $o->next_identity_sequence = $seq;
            $o->save();

            $code = $prefix ?: ($o->code ?: 'ORG');
            return $code . '-' . str_pad((string)$seq, $pad, '0', STR_PAD_LEFT);
        }, 1);
    }

    /** توليد رقم فاتورة: INV-YYYY-000001 */
    protected function nextInvoiceNumber(): string{
        $prefix = 'INV-' . now()->format('Y');
        $last = Invoice::where('number', 'like', $prefix . '%')
            ->orderByDesc('id')->value('number');

        $seq = 0;
        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $seq = (int)$m[1];
        }
        return sprintf('%s-%06d', $prefix, $seq + 1);
    }
}
