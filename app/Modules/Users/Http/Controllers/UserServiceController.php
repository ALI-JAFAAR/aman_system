<?php

namespace App\Modules\Users\Http\Controllers;

use App\Models\UserService;
use App\Modules\Shared\Http\Controllers\CrudController;

class UserServiceController extends CrudController
{
    protected string $modelClass = UserService::class;
}

