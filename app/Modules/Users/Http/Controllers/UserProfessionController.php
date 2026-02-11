<?php

namespace App\Modules\Users\Http\Controllers;

use App\Models\UserProfession;
use App\Modules\Shared\Http\Controllers\CrudController;

class UserProfessionController extends CrudController
{
    protected string $modelClass = UserProfession::class;
}

