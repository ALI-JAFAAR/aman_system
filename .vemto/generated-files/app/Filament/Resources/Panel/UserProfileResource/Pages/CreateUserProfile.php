<?php

namespace App\Filament\Resources\Panel\UserProfileResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Panel\UserProfileResource;

class CreateUserProfile extends CreateRecord
{
    protected static string $resource = UserProfileResource::class;
}
