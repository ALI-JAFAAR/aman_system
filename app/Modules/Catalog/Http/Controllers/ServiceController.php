<?php

namespace App\Modules\Catalog\Http\Controllers;

use App\Models\Service;
use App\Modules\Shared\Http\Controllers\CrudController;

class ServiceController extends CrudController
{
    protected string $modelClass = Service::class;
}

