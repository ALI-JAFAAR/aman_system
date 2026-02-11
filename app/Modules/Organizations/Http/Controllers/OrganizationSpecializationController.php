<?php

namespace App\Modules\Organizations\Http\Controllers;

use App\Models\OrganizationSpecialization;
use App\Modules\Shared\Http\Controllers\CrudController;

class OrganizationSpecializationController extends CrudController
{
    protected string $modelClass = OrganizationSpecialization::class;
}

