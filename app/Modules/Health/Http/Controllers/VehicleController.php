<?php

namespace App\Modules\Health\Http\Controllers;

use App\Models\Vehicle;
use App\Modules\Shared\Http\Controllers\CrudController;

class VehicleController extends CrudController
{
    protected string $modelClass = Vehicle::class;
}

