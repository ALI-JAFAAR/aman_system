<?php

namespace App\Modules\Catalog\Http\Controllers;

use App\Models\Profession;
use App\Modules\Shared\Http\Controllers\CrudController;

class ProfessionController extends CrudController
{
    protected string $modelClass = Profession::class;
}

