<?php

namespace App\Modules\Projects\Http\Controllers;

use App\Models\ProjectWorker;
use App\Modules\Shared\Http\Controllers\CrudController;

class ProjectWorkerController extends CrudController
{
    protected string $modelClass = ProjectWorker::class;
}

