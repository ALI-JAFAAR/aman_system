<?php

namespace App\Modules\Notifications\Http\Controllers;

use App\Models\Notification;
use App\Modules\Shared\Http\Controllers\CrudController;

class NotificationController extends CrudController
{
    protected string $modelClass = Notification::class;
}

