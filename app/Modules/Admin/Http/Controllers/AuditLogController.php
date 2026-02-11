<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Models\AuditLog;
use App\Modules\Shared\Http\Controllers\CrudController;

class AuditLogController extends CrudController
{
    protected string $modelClass = AuditLog::class;
}

