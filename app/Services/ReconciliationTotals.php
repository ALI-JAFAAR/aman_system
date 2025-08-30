<?php
namespace App\Services;

use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class ReconciliationTotals
{
    public static function forPartner(int $orgId, string $from, string $to): array
    {
        // join via user_offerings -> partner_offerings
        $q = InvoiceItem::query()
            ->join('invoices as inv', 'inv.id', '=', 'invoice_items.invoice_id')
            ->join('user_offerings as uo', function ($j) {
                $j->on('uo.id', '=', 'invoice_items.reference_id')
                    ->where('invoice_items.reference_type', '=', \App\Models\UserOffering::class);
            })
            ->join('partner_offerings as po', 'po.id', '=', 'uo.partner_offering_id')
            ->where('invoice_items.item_type', 'offering')
            ->where('po.organization_id', $orgId)
            ->whereBetween(DB::raw('DATE(inv.issued_at)'), [$from, $to]);

        return [
            'total_gross_amount'     => (float) $q->sum('invoice_items.line_total'),
            'total_platform_share'   => (float) $q->sum('invoice_items.platform_share'),
            'total_partner_share'    => (float) $q->sum('invoice_items.partner_share'),
            'total_organization_share'=> 0.0, // ليس لها معنى في تسوية الشريك
        ];
    }

    public static function forHost(int $orgId, string $from, string $to): array
    {
        $q = InvoiceItem::query()
            ->join('invoices as inv', 'inv.id', '=', 'invoice_items.invoice_id')
            ->where('invoice_items.item_type', 'offering')
            ->where('invoice_items.organization_id', $orgId)
            ->whereBetween(DB::raw('DATE(inv.issued_at)'), [$from, $to]);

        return [
            'total_gross_amount'      => (float) $q->sum('invoice_items.line_total'),
            'total_platform_share'    => (float) $q->sum('invoice_items.platform_share'),
            'total_partner_share'     => (float) $q->sum('invoice_items.partner_share'),
            'total_organization_share'=> (float) $q->sum('invoice_items.host_share'),
        ];
    }
}
