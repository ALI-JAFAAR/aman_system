<?php

namespace App\Modules\Users\Http\Controllers;

use App\Models\UserOffering;
use App\Modules\Shared\Http\Controllers\CrudController;

class UserOfferingController extends CrudController
{
    protected string $modelClass = UserOffering::class;
}

