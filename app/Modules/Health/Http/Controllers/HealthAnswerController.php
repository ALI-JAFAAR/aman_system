<?php

namespace App\Modules\Health\Http\Controllers;

use App\Models\HealthAnswer;
use App\Modules\Shared\Http\Controllers\CrudController;

class HealthAnswerController extends CrudController
{
    protected string $modelClass = HealthAnswer::class;
}

