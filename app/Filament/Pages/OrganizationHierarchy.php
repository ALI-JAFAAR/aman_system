<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Organization;

class OrganizationHierarchy extends Page{
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'الهيكل الهرمي';
    protected static ?int    $navigationSort  = 90;
    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-group';

    protected static string $view = 'filament.pages.organization-hierarchy';

    public array $tree = [];

    public function mount(): void{
        // جذور (بدون أب)
        $roots = Organization::whereNull('organization_id')
            ->orderBy('type')->orderBy('name')->get();

        $this->tree = $roots->map(fn($o) => $this->node($o))->all();
    }

    protected function node(Organization $org): array{
        $children = $org->organizations()->orderBy('type')->orderBy('name')->get();
        return [
            'id'   => $org->id,
            'name' => $org->name,
            'code' => $org->code,
            'type' => $org->type?->value ?? (string)$org->type,
            'children' => $children->map(fn($c) => $this->node($c))->all(),
        ];
    }
}
