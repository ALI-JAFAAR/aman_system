<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReconciliationTotals{
    public static function forPartner(int $orgId, string $from, string $to): array{
        $row = DB::table('invoice_items as ii')
            ->join('invoices as inv', 'inv.id', '=', 'ii.invoice_id')
            ->where('ii.item_type', 'offering')
            ->whereBetween(DB::raw('DATE(inv.issued_at)'), [$from, $to])
            ->join('user_offerings as uo', 'uo.id', '=', 'ii.reference_id')
            ->join('partner_offerings as po', 'po.id', '=', 'uo.partner_offering_id')
            ->where('po.organization_id', $orgId)
            ->selectRaw('
                COALESCE(SUM(ii.line_total),0)   as total_gross_amount,
                COALESCE(SUM(ii.platform_share),0) as total_platform_share,
                COALESCE(SUM(ii.partner_share),0)  as total_partner_share,
                0 as total_organization_share
            ')->first();

        return (array) $row;
    }

    public static function forHost(int $orgId, string $from, string $to): array{
        $row = DB::table('invoice_items as ii')
            ->join('invoices as inv', 'inv.id', '=', 'ii.invoice_id')
            ->where('ii.item_type', 'offering')
            ->whereBetween(DB::raw('DATE(inv.issued_at)'), [$from, $to])
            ->where('ii.organization_id', $orgId) // host org
            ->selectRaw('
                COALESCE(SUM(ii.line_total),0)   as total_gross_amount,
                COALESCE(SUM(ii.platform_share),0) as total_platform_share,
                COALESCE(SUM(ii.host_share),0)     as total_organization_share,
                0 as total_partner_share
            ')->first();

        return (array) $row;
    }
}
