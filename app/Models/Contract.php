<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'service_type',
        'initiator_type',
        'platform_rate',
        'organization_rate',
        'partner_rate',
        'contract_start',
        'contract_end',
        'notes',
        'partner_offering_id',
        'platform_share',
        'organization_share',
        'partner_share',
        'contract_version',
    ];

    protected $casts = [
        'contract_start' => 'date',
        'contract_end'   => 'date',
    ];

    public function scopeActive(Builder $q, $asOf = null): Builder{
        $asOf = $asOf ?: now()->toDateString();

        return $q
            // بداية العقد <= اليوم (أو لا يوجد بداية)
            ->where(function ($qq) use ($asOf) {
                $qq->whereNull('contract_start')
                    ->orWhereDate('contract_start', '<=', $asOf);
            })
            // نهاية العقد > اليوم (ملاحظة: أكبر من اليوم كما طلبت)
            ->where(function ($qq) use ($asOf) {
                $qq->whereNull('contract_end')
                    ->orWhereDate('contract_end', '>', $asOf);
            })
            // (اختياري) لو عندك عمود حالة
            ->when($this->getAttribute('status') !== null, fn ($qq) => $qq->where('status', 'active'));
    }

    public static function activeForOrganization(int $orgId, $asOf = null): ?self{
        return static::query()
            ->where('organization_id', $orgId)
            ->active($asOf)
            ->orderByDesc('id')       // لو تعددت العقود الفعّالة نأخذ الأحدث
            ->first();
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function reconciliations()
    {
        return $this->hasMany(Reconciliation::class);
    }

    public function partnerOffering()
    {
        return $this->belongsTo(PartnerOffering::class);
    }
}
