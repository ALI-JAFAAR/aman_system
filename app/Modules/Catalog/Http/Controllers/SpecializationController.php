<?php

namespace App\Modules\Catalog\Http\Controllers;

use App\Models\Specialization;
use App\Modules\Shared\Http\Controllers\CrudController;

class SpecializationController extends CrudController
{
    protected string $modelClass = Specialization::class;
}

