<?php

namespace App\Modules\Users\Http\Controllers;

use App\Models\UserAffiliation;
use App\Modules\Shared\Http\Controllers\CrudController;

class UserAffiliationController extends CrudController
{
    protected string $modelClass = UserAffiliation::class;
}

