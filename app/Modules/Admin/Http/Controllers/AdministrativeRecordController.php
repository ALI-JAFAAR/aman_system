<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Models\AdministrativeRecord;
use App\Modules\Shared\Http\Controllers\CrudController;

class AdministrativeRecordController extends CrudController
{
    protected string $modelClass = AdministrativeRecord::class;
}

