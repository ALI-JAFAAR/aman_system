<?php

namespace App\Filament\Resources\Panel\UserServiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Panel\UserServiceResource;

class CreateUserService extends CreateRecord
{
    protected static string $resource = UserServiceResource::class;
}
