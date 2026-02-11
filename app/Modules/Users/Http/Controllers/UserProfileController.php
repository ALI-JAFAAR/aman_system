<?php

namespace App\Modules\Users\Http\Controllers;

use App\Models\UserProfile;
use App\Modules\Shared\Http\Controllers\CrudController;

class UserProfileController extends CrudController
{
    protected string $modelClass = UserProfile::class;
}

