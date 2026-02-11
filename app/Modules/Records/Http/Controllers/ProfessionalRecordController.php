<?php

namespace App\Modules\Records\Http\Controllers;

use App\Models\ProfessionalRecord;
use App\Modules\Shared\Http\Controllers\CrudController;

class ProfessionalRecordController extends CrudController
{
    protected string $modelClass = ProfessionalRecord::class;
}

